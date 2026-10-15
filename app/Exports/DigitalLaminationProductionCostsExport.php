<?php

namespace App\Exports;

use App\Support\DigitalLaminationConfiguration;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DigitalLaminationProductionCostsExport extends DefaultValueBinder implements FromArray, WithEvents, WithTitle, WithCustomValueBinder
{
    private const FIXED_COLUMNS = 6;
    private const TIERS = [
        'tier_1' => '1 lbr', 'tier_2_5' => '2-5 lbr', 'tier_6_50' => '6-50 lbr',
        'tier_100' => '100 lbr', 'tier_300' => '300 lbr', 'tier_500' => '500 lbr',
    ];
    private const GROUPS = [
        ['general', 'single', 'HARGA UMUM - LAMINASI 1 MUKA', 'FEF3C7'],
        ['general', 'double', 'HARGA UMUM - LAMINASI 2 MUKA', 'FEF3C7'],
        ['division', 'single', 'HARGA DIVISI - LAMINASI 1 MUKA', 'D1FAE5'],
        ['division', 'double', 'HARGA DIVISI - LAMINASI 2 MUKA', 'D1FAE5'],
    ];
    private array $sectionRows = [];
    private array $tables = [];

    public function bindValue(Cell $cell, $value): bool
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function array(): array
    {
        $this->sectionRows = [];
        $this->tables = [];
        $details = DB::table('m_digital_lamination_production_cost_details as d')
            ->join('m_digital_lamination_production_costs as pc', 'pc.id', '=', 'd.digital_lamination_production_cost_id')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->join('m_laminations as lm', 'lm.id', '=', 'd.lamination_id')
            ->join('m_categories as c', 'c.id', '=', 'd.category_id')
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['digital print a3+'])
            ->select('d.*', 'e.name as engine_name', 'l.name as location_name', 'v.name as vendor_name',
                'lm.name as lamination_name', 'c.name as category_name')
            ->orderBy('l.name')->orderBy('v.name')->orderBy('pc.id')->orderBy('c.name')
            ->orderBy('lm.name')->orderBy('d.id')->get();
        $prices = DB::table('m_digital_lamination_production_cost_prices')
            ->whereIn('digital_lamination_production_cost_detail_id', $details->pluck('id'))->get()
            ->groupBy('digital_lamination_production_cost_detail_id')
            ->map(fn($items) => $items->keyBy(fn($price) => $price->price_type . '|' . $price->print_type . '|' . $price->quantity_tier));
        $rows = [$this->pad(['ONGKOS PRODUKSI LAMINASI DIGITAL PRINT A3+']), $this->pad([])];
        if ($details->isEmpty()) {
            $rows[] = $this->pad(['Belum ada data ongkos produksi laminasi Digital Print A3+.']);

            return $rows;
        }
        foreach ($details->groupBy('digital_lamination_production_cost_id') as $headerDetails) {
            $header = $headerDetails->first();
            foreach ([
                ['MESIN', $header->engine_name], ['LOKASI', $header->location_name],
                ['VENDOR', $header->vendor_name ?: '-'],
            ] as $section) {
                $rows[] = $this->pad($section);
                $this->sectionRows[] = count($rows);
            }
            $categoryGroups = $headerDetails->groupBy('category_id')->groupBy(
                fn($categoryDetails) => DigitalLaminationConfiguration::signature($categoryDetails, $prices)
            );
            foreach ($categoryGroups as $sameCategories) {
                $categoryDetails = $sameCategories->first();
                $categoryNames = $sameCategories->map(fn($category) => $category->first()->category_name)->implode(', ');
                $rows[] = $this->pad(['KATEGORI', $categoryNames]);
                $this->sectionRows[] = count($rows);
                $groupRow = ['No', 'Laminasi', 'Harga Satuan', 'Harga Klik', 'Cost Laminasi 1 Sisi', 'Cost Laminasi 2 Sisi'];
                $tierRow = array_fill(0, self::FIXED_COLUMNS, '');
                $markupRow = $tierRow;
                foreach (self::GROUPS as [$priceType, $printType, $label]) {
                    foreach (self::TIERS as $tier => $tierLabel) {
                        $key = "$priceType|$printType|$tier";
                        $groupRow[] = $tier === 'tier_1' ? $label : '';
                        $tierRow[] = $tierLabel;
                        $markups = $categoryDetails->map(fn($detail) => $prices->get($detail->id)?->get($key))
                            ->filter()->map(fn($price) => rtrim(rtrim(number_format((float) $price->markup_percentage, 2, ',', ''), '0'), ',') . '%')
                            ->unique()->values();
                        $markupRow[] = $markups->isEmpty() ? '-' : $markups->implode(' / ');
                    }
                }
                $rows[] = $groupRow;
                $headerRow = count($rows);
                $rows[] = $tierRow;
                $rows[] = $markupRow;
                foreach ($categoryDetails->values() as $index => $detail) {
                    $row = [$index + 1, $detail->lamination_name, (float) $detail->paper_cost,
                        (float) $detail->click_cost, (float) $detail->single_print_cost, (float) $detail->double_print_cost];
                    foreach (self::GROUPS as [$priceType, $printType]) {
                        foreach (array_keys(self::TIERS) as $tier) {
                            $price = $prices->get($detail->id)?->get("$priceType|$printType|$tier");
                            $row[] = $price ? (float) $price->selling_price : '-';
                        }
                    }
                    $rows[] = $row;
                }
                $this->tables[] = [$headerRow, count($rows)];
                $rows[] = $this->pad([]);
            }
            $rows[] = $this->pad([]);
        }

        return $rows;
    }

    private function pad(array $row): array
    {
        return array_pad($row, self::FIXED_COLUMNS + count(self::TIERS) * count(self::GROUPS), '');
    }

    public function title(): string
    {
        return 'Digital Print A3+';
    }

    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $lastColumn = Coordinate::stringFromColumnIndex(count($this->pad([])));
            $sheet->getDefaultColumnDimension()->setWidth(18);
            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(38);
            $sheet->mergeCells("A1:{$lastColumn}1");
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            foreach ($this->sectionRows as $row) {
                $sheet->mergeCells("B{$row}:{$lastColumn}{$row}");
                $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFont()->setBold(true);
                $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
            }
            foreach ($this->tables as [$header, $end]) {
                $markup = $header + 2;
                $start = $header + 3;
                for ($column = 1; $column <= self::FIXED_COLUMNS; $column++) {
                    $letter = Coordinate::stringFromColumnIndex($column);
                    $sheet->mergeCells("{$letter}{$header}:{$letter}{$markup}");
                }
                $sheet->getStyle("A{$header}:{$lastColumn}{$markup}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                ]);
                foreach (self::GROUPS as $index => [, , , $color]) {
                    $first = Coordinate::stringFromColumnIndex(self::FIXED_COLUMNS + 1 + $index * count(self::TIERS));
                    $last = Coordinate::stringFromColumnIndex(self::FIXED_COLUMNS + ($index + 1) * count(self::TIERS));
                    $sheet->mergeCells("{$first}{$header}:{$last}{$header}");
                    $sheet->getStyle("{$first}{$header}:{$last}{$end}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
                }
                $sheet->getStyle("A{$header}:{$lastColumn}{$end}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("C{$start}:{$lastColumn}{$end}")->getNumberFormat()->setFormatCode('"Rp"#,##0.00');
                $sheet->getStyle("C{$start}:{$lastColumn}{$end}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("E{$start}:F{$end}")->getFont()->setBold(true);
            }
            $sheet->freezePane('C3');
        }];
    }
}
