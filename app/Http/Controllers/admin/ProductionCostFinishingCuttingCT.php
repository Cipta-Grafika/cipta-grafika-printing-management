<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductionCostFinishingCuttingCT extends Controller
{
    public function index()
    {
        return view("admin.production-cost-finishing-cutting.index");
    }

    public function data(Request $request)
    {
        $filter = $request->input('cutting_filter', 'cutting');
        if ($filter === 'potong') {
            return $this->dataPotong($request);
        }

        $query = DB::table('m_digital_cutting_production_costs as pc')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->leftJoin('m_digital_cutting_production_cost_details as d', 'd.digital_cutting_production_cost_id', '=', 'pc.id')
            ->select(
                'pc.id as digital_cutting_production_cost_id',
                'e.id as engine_id',
                'e.name as engine_name',
                'l.name as location_names',
                'v.name as vendor_name',
                DB::raw('COUNT(DISTINCT d.cutting_id) as cutting_count')
            )
            ->groupBy('pc.id', 'e.id', 'e.name', 'l.name', 'v.name');

        $totalRecords = (clone $query)->get()->count();
        $search = $request->input('search.value');

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(e.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(l.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(v.name) LIKE ?', [$search])
                    ->orWhereExists(function ($categories) use ($search) {
                        $categories->selectRaw('1')
                            ->from('m_digital_cutting_production_cost_details as cd')
                            ->join('m_categories as c', 'c.id', '=', 'cd.category_id')
                            ->whereColumn('cd.digital_cutting_production_cost_id', 'pc.id')
                            ->whereRaw('LOWER(c.name) LIKE ?', [$search]);
                    })
                    ->orWhereExists(function ($cuttings) use ($search) {
                        $cuttings->selectRaw('1')
                            ->from('m_digital_cutting_production_cost_details as cd')
                            ->join('m_cuttings as ct', 'ct.id', '=', 'cd.cutting_id')
                            ->whereColumn('cd.digital_cutting_production_cost_id', 'pc.id')
                            ->whereRaw('LOWER(ct.name) LIKE ?', [$search]);
                    });
            });
        }

        $totalFiltered = (clone $query)->get()->count();
        $data = $query
            ->orderBy('e.name')
            ->orderBy('l.name')
            ->orderBy('v.name')
            ->orderBy('pc.id')
            ->offset((int) $request->input('start', 0))
            ->limit((int) $request->input('length', 10))
            ->get();

        $categoryNames = DB::table('m_digital_cutting_production_cost_details as d')
            ->join('m_categories as c', 'c.id', '=', 'd.category_id')
            ->whereIn('d.digital_cutting_production_cost_id', $data->pluck('digital_cutting_production_cost_id'))
            ->select('d.digital_cutting_production_cost_id', 'c.id as category_id', 'c.name')
            ->distinct()
            ->orderBy('c.name')
            ->get()
            ->groupBy('digital_cutting_production_cost_id');

        $data->each(function ($row) use ($categoryNames) {
            $row->category_count = $categoryNames
                ->get($row->digital_cutting_production_cost_id, collect())
                ->count();
            $row->category_names = $categoryNames
                ->get($row->digital_cutting_production_cost_id, collect())
                ->pluck('name')
                ->implode(', ');
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['categories'] = DB::table('m_categories')
            ->orderBy('name')
            ->get();

        $data['cuttings'] = DB::table('m_cuttings')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['vendors'] = DB::table('m_vendors')
            ->where('status', 'Active')
            ->get();

        $data['default_cutting_type'] = request()->query('type', 'cutting');

        return view(
            'admin.production-cost-finishing-cutting.create'
        )->with($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'configurations' => 'required|array|min:1',
            'configurations.*' => 'required|array',
            'configurations.*.engine_id' => 'required|integer|exists:m_engines,id',
            'configurations.*.location_ids' => 'required|array|min:1',
            'configurations.*.location_ids.*' => 'required|integer|exists:m_locations,id',
            'configurations.*.category_ids' => 'required|array|min:1',
            'configurations.*.category_ids.*' => 'required|integer|exists:m_categories,id',
            'configurations.*.vendor_id' => 'nullable|integer|exists:m_vendors,id',
            'configurations.*.items' => 'required|array|min:1',
            'configurations.*.items.*' => 'required|array',
            'configurations.*.items.*.cutting_id' => 'required|integer|exists:m_cuttings,id',
            'configurations.*.items.*.prices' => 'nullable|array',
            'configurations.*.items.*.potong_prices' => 'nullable|array',
        ], [
            'configurations.required' => 'Minimal harus ada 1 blok konfigurasi.',
            'configurations.*.engine_id.required' => 'Mesin wajib dipilih.',
            'configurations.*.location_ids.required' => 'Minimal pilih satu lokasi.',
            'configurations.*.category_ids.required' => 'Minimal pilih satu kategori kertas/bahan.',
            'configurations.*.items.required' => 'Minimal harus ada satu jasa potong dalam konfigurasi.',
            'configurations.*.items.*.cutting_id.required' => 'Jasa potong wajib dipilih.',
        ]);

        $tierDefinitions = [
            'tier_1' => ['min' => 1, 'max' => 1, 'label' => '1 lbr'],
            'tier_2_5' => ['min' => 2, 'max' => 5, 'label' => '2-5 lbr'],
            'tier_6_50' => ['min' => 6, 'max' => 50, 'label' => '6-50 lbr'],
            'tier_100' => ['min' => 51, 'max' => 100, 'label' => '100 lbr'],
            'tier_300' => ['min' => 101, 'max' => 300, 'label' => '300 lbr'],
            'tier_500' => ['min' => 301, 'max' => 500, 'label' => '500 lbr'],
        ];
        $tierKeys = array_keys($tierDefinitions);
        $maximumAmount = 9999999999.99;
        $userName = Auth::user()->name ?? 'System';

        try {
            DB::transaction(function () use ($request, $tierDefinitions, $tierKeys, $maximumAmount, $userName) {
                $engineIds = collect($request->input('configurations'))->pluck('engine_id')->unique();
                $engines = DB::table('m_engines')
                    ->whereIn('id', $engineIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $locations = DB::table('m_locations')->get()->keyBy('id');
                $categories = DB::table('m_categories')->get()->keyBy('id');
                $cuttings = DB::table('m_cuttings')->where('status', 'Active')->get()->keyBy('id');
                $vendors = DB::table('m_vendors')->where('status', 'Active')->get()->keyBy('id');

                $usedKeys = [];

                foreach ($request->input('configurations') as $confIndex => $configuration) {
                    $prefix = 'configurations.' . $confIndex;
                    $engineId = (int) $configuration['engine_id'];
                    $engine = $engines->get($engineId);

                    if (!$engine) {
                        throw ValidationException::withMessages([
                            $prefix . '.engine_id' => 'Mesin yang dipilih tidak valid.'
                        ]);
                    }

                    if (strtolower(trim($engine->status)) !== 'active') {
                        throw ValidationException::withMessages([
                            $prefix . '.engine_id' => 'Mesin "' . $engine->name . '" tidak aktif.'
                        ]);
                    }

                    $locationIds = array_map('intval', $configuration['location_ids']);
                    $categoryIds = array_map('intval', $configuration['category_ids']);

                    if (count($locationIds) !== count(array_unique($locationIds))) {
                        throw ValidationException::withMessages([
                            $prefix . '.location_ids' => 'Lokasi tidak boleh dipilih lebih dari satu kali dalam satu konfigurasi.'
                        ]);
                    }

                    if (count($categoryIds) !== count(array_unique($categoryIds))) {
                        throw ValidationException::withMessages([
                            $prefix . '.category_ids' => 'Kategori kertas tidak boleh dipilih lebih dari satu kali dalam satu konfigurasi.'
                        ]);
                    }

                    $isOutsourcing = false;
                    foreach ($locationIds as $locId) {
                        $loc = $locations->get($locId);
                        if (!$loc || strtolower(trim($loc->status)) !== 'active') {
                            throw ValidationException::withMessages([
                                $prefix . '.location_ids' => 'Lokasi tidak ditemukan atau tidak aktif.'
                            ]);
                        }
                        if (strtolower(trim($loc->name)) === 'outsourcing') {
                            $isOutsourcing = true;
                        }
                    }

                    if ($isOutsourcing && count($locationIds) > 1) {
                        throw ValidationException::withMessages([
                            $prefix . '.location_ids' => 'Lokasi Outsourcing tidak dapat digabung dengan lokasi lain.'
                        ]);
                    }

                    $vendorId = null;
                    if ($isOutsourcing) {
                        $vendorId = !empty($configuration['vendor_id']) ? (int) $configuration['vendor_id'] : null;
                        if (!$vendorId || !$vendors->has($vendorId)) {
                            throw ValidationException::withMessages([
                                $prefix . '.vendor_id' => 'Master Vendor wajib dipilih dan harus aktif untuk lokasi Outsourcing.'
                            ]);
                        }
                    }

                    // Check duplicate cutting items in this block & validate prices
                    $blockCuttingIds = [];
                    foreach ($configuration['items'] as $itemIndex => $item) {
                        $itemPrefix = $prefix . '.items.' . $itemIndex;
                        $cuttingId = (int) ($item['cutting_id'] ?? 0);
                        $cutting = $cuttings->get($cuttingId);

                        if (!$cutting) {
                            throw ValidationException::withMessages([
                                $itemPrefix . '.cutting_id' => 'Jasa potong tidak valid atau tidak aktif.'
                            ]);
                        }

                        if (in_array($cuttingId, $blockCuttingIds, true)) {
                            throw ValidationException::withMessages([
                                $itemPrefix . '.cutting_id' => 'Jasa potong "' . $cutting->name . '" duplikat dalam satu konfigurasi.'
                            ]);
                        }
                        $blockCuttingIds[] = $cuttingId;

                        $isPotong = strtolower(trim($cutting->name)) === 'potong';

                        if ($isPotong) {
                            $potongSizes = ['A3', 'A4', 'A5', '<=A6'];
                            $potongTypes = [
                                'general' => 'Harga Umum',
                                'division' => 'Harga Divisi'
                            ];

                            foreach ($potongTypes as $pType => $pTypeLabel) {
                                foreach ($potongSizes as $pSize) {
                                    $rawPrice = $item['potong_prices'][$pType][$pSize] ?? null;
                                    $priceVal = $this->parseMoney($rawPrice);

                                    if ($priceVal === null) {
                                        throw ValidationException::withMessages([
                                            $itemPrefix . '.potong_prices.' . $pType . '.' . $pSize =>
                                            $pTypeLabel . ' ukuran ' . $pSize . ' pada ongkos potong wajib diisi.'
                                        ]);
                                    }

                                    if ($priceVal < 0 || $priceVal > $maximumAmount) {
                                        throw ValidationException::withMessages([
                                            $itemPrefix . '.potong_prices.' . $pType . '.' . $pSize =>
                                            $pTypeLabel . ' ukuran ' . $pSize . ' harus antara 0 dan 9.999.999.999,99.'
                                        ]);
                                    }
                                }
                            }
                        } else {
                            // Validate tier prices
                            foreach ($tierKeys as $tierKey) {
                                $tierLabel = $tierDefinitions[$tierKey]['label'];
                                $rawPrice = $item['prices']['general'][$tierKey] ?? null;

                                $priceVal = $this->parseMoney($rawPrice);
                                if ($priceVal === null) {
                                    throw ValidationException::withMessages([
                                        $itemPrefix . '.prices.general.' . $tierKey =>
                                        'Harga Umum (' . $tierLabel . ') pada jasa potong "' . $cutting->name . '" wajib diisi.'
                                    ]);
                                }

                                if ($priceVal < 0 || $priceVal > $maximumAmount) {
                                    throw ValidationException::withMessages([
                                        $itemPrefix . '.prices.general.' . $tierKey =>
                                        'Harga Umum (' . $tierLabel . ') pada jasa potong "' . $cutting->name . '" harus antara 0 dan 9.999.999.999,99.'
                                    ]);
                                }
                            }
                        }
                    }

                    // Check if block has cutting or potong items
                    $hasCuttingItems = collect($configuration['items'])->contains(function ($it) use ($cuttings) {
                        $c = $cuttings->get((int) ($it['cutting_id'] ?? 0));
                        return $c && strtolower(trim($c->name)) !== 'potong';
                    });

                    $hasPotongItems = collect($configuration['items'])->contains(function ($it) use ($cuttings) {
                        $c = $cuttings->get((int) ($it['cutting_id'] ?? 0));
                        return $c && strtolower(trim($c->name)) === 'potong';
                    });

                    // Insert/Match headers, details, and prices
                    foreach ($locationIds as $locationId) {
                        $loc = $locations->get($locationId);
                        $scopeLabel = $loc->name . ($vendorId ? ' - ' . $vendors->get($vendorId)->name : '');
                        $audit = ['updated_by' => $userName, 'updated_at' => now()];

                        $cuttingHeaderId = null;
                        if ($hasCuttingItems) {
                            $header = DB::table('m_digital_cutting_production_costs')
                                ->where('engine_id', $engineId)
                                ->where('location_id', $locationId)
                                ->where(function ($q) use ($vendorId) {
                                    if ($vendorId === null) {
                                        $q->whereNull('vendor_id');
                                    } else {
                                        $q->where('vendor_id', $vendorId);
                                    }
                                })
                                ->lockForUpdate()
                                ->first();

                            if ($header) {
                                $cuttingHeaderId = $header->id;
                                DB::table('m_digital_cutting_production_costs')
                                    ->where('id', $cuttingHeaderId)
                                    ->update($audit);
                            } else {
                                $cuttingHeaderId = DB::table('m_digital_cutting_production_costs')->insertGetId([
                                    'engine_id' => $engineId,
                                    'location_id' => $locationId,
                                    'vendor_id' => $vendorId,
                                    'created_by' => $userName,
                                    'created_at' => now(),
                                ] + $audit);
                            }
                        }

                        $potongHeaderId = null;
                        if ($hasPotongItems) {
                            $headerP = DB::table('m_digital_potong_production_costs')
                                ->where('engine_id', $engineId)
                                ->where('location_id', $locationId)
                                ->where(function ($q) use ($vendorId) {
                                    if ($vendorId === null) {
                                        $q->whereNull('vendor_id');
                                    } else {
                                        $q->where('vendor_id', $vendorId);
                                    }
                                })
                                ->lockForUpdate()
                                ->first();

                            if ($headerP) {
                                $potongHeaderId = $headerP->id;
                                DB::table('m_digital_potong_production_costs')
                                    ->where('id', $potongHeaderId)
                                    ->update($audit);
                            } else {
                                $potongHeaderId = DB::table('m_digital_potong_production_costs')->insertGetId([
                                    'engine_id' => $engineId,
                                    'location_id' => $locationId,
                                    'vendor_id' => $vendorId,
                                    'created_by' => $userName,
                                    'created_at' => now(),
                                ] + $audit);
                            }
                        }

                        foreach ($categoryIds as $categoryId) {
                            $cat = $categories->get($categoryId);
                            if (!$cat) {
                                throw ValidationException::withMessages([
                                    $prefix . '.category_ids' => 'Kategori kertas tidak ditemukan.'
                                ]);
                            }

                            foreach ($configuration['items'] as $itemIndex => $item) {
                                $itemPrefix = $prefix . '.items.' . $itemIndex;
                                $cuttingId = (int) $item['cutting_id'];
                                $cutting = $cuttings->get($cuttingId);
                                $isPotong = strtolower(trim($cutting->name)) === 'potong';

                                if ($isPotong) {
                                    $batchKey = 'potong|' . $engineId . '|' . $locationId . '|' . ($vendorId ?? 'null') . '|' . $categoryId;
                                    if (isset($usedKeys[$batchKey])) {
                                        throw ValidationException::withMessages([
                                            $itemPrefix . '.cutting_id' =>
                                            'Ongkos produksi potong untuk kategori "' . $cat->name . '" di lokasi ' . $scopeLabel . ' duplikat dalam input formulir.'
                                        ]);
                                    }
                                    $usedKeys[$batchKey] = true;

                                    $exists = DB::table('m_digital_potong_production_cost_categories as d')
                                        ->join('m_digital_potong_production_costs as h', 'd.digital_potong_production_cost_id', '=', 'h.id')
                                        ->where('h.engine_id', $engineId)
                                        ->where('h.location_id', $locationId)
                                        ->where(function ($q) use ($vendorId) {
                                            if ($vendorId === null) {
                                                $q->whereNull('h.vendor_id');
                                            } else {
                                                $q->where('h.vendor_id', $vendorId);
                                            }
                                        })
                                        ->where('d.category_id', $categoryId)
                                        ->exists();

                                    if ($exists) {
                                        throw ValidationException::withMessages([
                                            $itemPrefix . '.cutting_id' =>
                                            'Ongkos produksi potong untuk kategori "' . $cat->name . '" di lokasi ' . $scopeLabel . ' sudah terdaftar sebelumnya.'
                                        ]);
                                    }

                                    $catId = DB::table('m_digital_potong_production_cost_categories')->insertGetId([
                                        'digital_potong_production_cost_id' => $potongHeaderId,
                                        'category_id' => $categoryId,
                                        'created_by' => $userName,
                                        'created_at' => now(),
                                    ] + $audit);

                                    $potongPriceRows = [];
                                    foreach (['general', 'division'] as $pType) {
                                        foreach (['A3', 'A4', 'A5', '<=A6'] as $pSize) {
                                            $rawPrice = $item['potong_prices'][$pType][$pSize] ?? null;
                                            $priceVal = (float) $this->parseMoney($rawPrice);
                                            $potongPriceRows[] = [
                                                'digital_potong_production_cost_category_id' => $catId,
                                                'size_class' => $pSize,
                                                'price_type' => $pType,
                                                'min_price' => $priceVal,
                                                'created_by' => $userName,
                                                'created_at' => now(),
                                                'updated_by' => $userName,
                                                'updated_at' => now(),
                                            ];
                                        }
                                    }
                                    DB::table('m_digital_potong_production_cost_prices')->insert($potongPriceRows);

                                } else {
                                    $batchKey = 'cutting|' . $engineId . '|' . $locationId . '|' . ($vendorId ?? 'null') . '|' . $categoryId . '|' . $cuttingId;
                                    if (isset($usedKeys[$batchKey])) {
                                        throw ValidationException::withMessages([
                                            $itemPrefix . '.cutting_id' =>
                                            'Ongkos produksi jasa potong "' . $cutting->name . '" untuk kategori "' .
                                            $cat->name . '" di lokasi ' . $scopeLabel . ' duplikat dalam input formulir.'
                                        ]);
                                    }
                                    $usedKeys[$batchKey] = true;

                                    $exists = DB::table('m_digital_cutting_production_cost_details as d')
                                        ->join('m_digital_cutting_production_costs as h', 'd.digital_cutting_production_cost_id', '=', 'h.id')
                                        ->where('h.engine_id', $engineId)
                                        ->where('h.location_id', $locationId)
                                        ->where(function ($q) use ($vendorId) {
                                            if ($vendorId === null) {
                                                $q->whereNull('h.vendor_id');
                                            } else {
                                                $q->where('h.vendor_id', $vendorId);
                                            }
                                        })
                                        ->where('d.category_id', $categoryId)
                                        ->where('d.cutting_id', $cuttingId)
                                        ->exists();

                                    if ($exists) {
                                        throw ValidationException::withMessages([
                                            $itemPrefix . '.cutting_id' =>
                                            'Ongkos produksi jasa potong "' . $cutting->name . '" untuk kategori "' .
                                            $cat->name . '" di lokasi ' . $scopeLabel . ' sudah terdaftar sebelumnya.'
                                        ]);
                                    }

                                    // Insert detail
                                    $detailId = DB::table('m_digital_cutting_production_cost_details')->insertGetId([
                                        'digital_cutting_production_cost_id' => $cuttingHeaderId,
                                        'cutting_id' => $cuttingId,
                                        'category_id' => $categoryId,
                                        'created_by' => $userName,
                                        'created_at' => now(),
                                    ] + $audit);

                                    // Prepare prices (12 baris: 6 general + 6 division)
                                    $priceRows = [];
                                    foreach ($tierKeys as $tierKey) {
                                        $rawGeneralPrice = $item['prices']['general'][$tierKey] ?? null;
                                        $generalPrice = (float) $this->parseMoney($rawGeneralPrice);
                                        $divisionPrice = round($generalPrice * 0.8, 2);

                                        // Row General (Harga Umum)
                                        $priceRows[] = [
                                            'digital_cutting_production_cost_detail_id' => $detailId,
                                            'price_type' => 'general',
                                            'quantity_tier' => $tierKey,
                                            'minimum_quantity' => $tierDefinitions[$tierKey]['min'],
                                            'maximum_quantity' => $tierDefinitions[$tierKey]['max'],
                                            'price' => $generalPrice,
                                            'created_by' => $userName,
                                            'created_at' => now(),
                                        ] + $audit;

                                        // Row Division (Harga Divisi 80%)
                                        $priceRows[] = [
                                            'digital_cutting_production_cost_detail_id' => $detailId,
                                            'price_type' => 'division',
                                            'quantity_tier' => $tierKey,
                                            'minimum_quantity' => $tierDefinitions[$tierKey]['min'],
                                            'maximum_quantity' => $tierDefinitions[$tierKey]['max'],
                                            'price' => $divisionPrice,
                                            'created_by' => $userName,
                                            'created_at' => now(),
                                        ] + $audit;
                                    }

                                    DB::table('m_digital_cutting_production_cost_prices')->insert($priceRows);
                                }
                            }
                        }
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Ongkos produksi berhasil disimpan.'
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat menyimpan ongkos produksi.'
            ], 500);
        }
    }

    private function parseMoney($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $str = trim((string) $value);
        if ($str === '') {
            return null;
        }

        // Format Rupiah: titik (.) adalah pemisah ribuan, koma (,) adalah pemisah desimal
        // Contoh: "10.000" -> 10000, "9.500" -> 9500, "10.000,50" -> 10000.50
        $clean = str_replace('.', '', $str);
        $clean = str_replace(',', '.', $clean);

        return is_numeric($clean) ? (float) $clean : null;
    }

    private function findProductionCost($id)
    {
        $decodedId = base64_decode($id, true);
        if ($decodedId === false || !ctype_digit($decodedId) || (int) $decodedId <= 0) {
            $decodedId = (int) $id;
        }

        return DB::table('m_digital_cutting_production_costs as pc')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->where('pc.id', $decodedId)
            ->select('pc.*', 'e.name as engine_name', 'l.name as location_name', 'v.name as vendor_name')
            ->first();
    }

    public function edit($id)
    {
        $productionCost = $this->findProductionCost($id);
        abort_unless($productionCost, 404);

        $details = DB::table('m_digital_cutting_production_cost_details as d')
            ->where('d.digital_cutting_production_cost_id', $productionCost->id)
            ->orderBy('d.category_id')
            ->orderBy('d.cutting_id')
            ->orderBy('d.id')
            ->get();

        $prices = DB::table('m_digital_cutting_production_cost_prices')
            ->whereIn('digital_cutting_production_cost_detail_id', $details->pluck('id'))
            ->get()
            ->groupBy('digital_cutting_production_cost_detail_id');

        $tierKeys = ['tier_1', 'tier_2_5', 'tier_6_50', 'tier_100', 'tier_300', 'tier_500'];

        $groups = $details->groupBy('category_id')
            ->map(function ($categoryDetails, $categoryId) use ($prices, $tierKeys) {
                $items = $categoryDetails->map(function ($detail) use ($prices, $tierKeys) {
                    $detailPrices = $prices->get($detail->id, collect());
                    $generalPrices = $detailPrices->where('price_type', 'general')->keyBy('quantity_tier');
                    $divisionPrices = $detailPrices->where('price_type', 'division')->keyBy('quantity_tier');

                    $itemPrices = [
                        'general' => [],
                        'division' => [],
                    ];

                    foreach ($tierKeys as $tierKey) {
                        $gen = $generalPrices->get($tierKey);
                        $div = $divisionPrices->get($tierKey);

                        $itemPrices['general'][$tierKey] = $gen ? number_format((float) $gen->price, 0, ',', '.') : '';
                        $itemPrices['division'][$tierKey] = $div ? number_format((float) $div->price, 0, ',', '.') : '';
                    }

                    return [
                        'cutting_id' => (int) $detail->cutting_id,
                        'prices' => $itemPrices,
                    ];
                })->values()->all();

                return [
                    'category_ids' => [(int) $categoryId],
                    'items' => $items,
                ];
            })
            ->groupBy(fn($group) => json_encode($group['items']))
            ->map(fn($sameGroups) => [
                'category_ids' => $sameGroups->pluck('category_ids')->flatten()->values()->all(),
                'items' => $sameGroups->first()['items'],
            ])
            ->values();

        $cuttings = DB::table('m_cuttings')
            ->where(function ($query) use ($details) {
                $query->where('status', 'Active')
                    ->orWhereIn('id', $details->pluck('cutting_id'));
            })
            ->orderBy('name')
            ->get();

        $categories = DB::table('m_categories')
            ->orderBy('name')
            ->get();

        return view('admin.production-cost-finishing-cutting.edit', compact(
            'productionCost',
            'groups',
            'cuttings',
            'categories'
        ));
    }

    public function details($id)
    {
        $productionCost = $this->findProductionCost($id);
        abort_unless($productionCost, 404);

        $details = DB::table('m_digital_cutting_production_cost_details as d')
            ->join('m_cuttings as ct', 'ct.id', '=', 'd.cutting_id')
            ->join('m_categories as c', 'c.id', '=', 'd.category_id')
            ->where('d.digital_cutting_production_cost_id', $productionCost->id)
            ->select('d.*', 'ct.name as cutting_name', 'c.name as category_name')
            ->orderBy('c.name')
            ->orderBy('ct.name')
            ->orderBy('d.id')
            ->get();

        $prices = DB::table('m_digital_cutting_production_cost_prices')
            ->whereIn('digital_cutting_production_cost_detail_id', $details->pluck('id'))
            ->get()
            ->groupBy('digital_cutting_production_cost_detail_id');

        $tierLabels = [
            'tier_1' => '1 lbr',
            'tier_2_5' => '2-5 lbr',
            'tier_6_50' => '6-50 lbr',
            'tier_100' => '100 lbr',
            'tier_300' => '300 lbr',
            'tier_500' => '500 lbr',
        ];

        $priceGroups = [
            'general' => 'Harga Umum',
            'division' => 'Harga Divisi (80%)',
        ];

        $details->each(function ($detail) use ($prices, $priceGroups, $tierLabels) {
            $detailPrices = ($prices->get($detail->id) ?? collect())->keyBy(
                fn($price) => $price->price_type . '|' . $price->quantity_tier
            );
            $detail->prices = [];
            foreach (array_keys($priceGroups) as $groupKey) {
                foreach (array_keys($tierLabels) as $tier) {
                    $detail->prices[$groupKey][$tier] = $detailPrices->get($groupKey . '|' . $tier);
                }
            }
        });

        $categories = $details->groupBy('category_id')->map(function ($categoryDetails) {
            return (object) [
                'category_name' => $categoryDetails->first()->category_name,
                'details' => $categoryDetails->values(),
            ];
        })->values();

        // Gabungkan kategori yang memiliki konfigurasi jasa potong dan harga yang sama persis
        $categories = $categories->groupBy(function ($category) {
            return $category->details->map(function ($d) {
                $pricesFlat = [];
                foreach ($d->prices as $pt => $tiers) {
                    foreach ($tiers as $tk => $p) {
                        $pricesFlat[] = $pt . ':' . $tk . ':' . ($p ? $p->price : '0');
                    }
                }
                return $d->cutting_id . '|' . implode(',', $pricesFlat);
            })->implode(';');
        })->map(function ($sameCategories) {
            $category = $sameCategories->first();
            $category->category_names = $sameCategories->pluck('category_name')->values();
            return $category;
        })->values();

        return view('admin.production-cost-finishing-cutting.details', compact(
            'productionCost',
            'categories',
            'tierLabels',
            'priceGroups'
        ));
    }

    public function update(Request $request, $id)
    {
        $productionCost = $this->findProductionCost($id);
        abort_unless($productionCost, 404);

        $request->validate([
            'configurations' => 'required|array|min:1',
            'configurations.*' => 'required|array',
            'configurations.*.category_ids' => 'required|array|min:1',
            'configurations.*.category_ids.*' => 'required|integer|exists:m_categories,id',
            'configurations.*.items' => 'required|array|min:1',
            'configurations.*.items.*' => 'required|array',
            'configurations.*.items.*.cutting_id' => 'required|integer|exists:m_cuttings,id',
            'configurations.*.items.*.prices' => 'required|array',
            'configurations.*.items.*.prices.general' => 'required|array',
        ], [
            'configurations.required' => 'Minimal harus ada 1 kelompok konfigurasi.',
            'configurations.*.category_ids.required' => 'Minimal pilih satu kategori kertas/bahan.',
            'configurations.*.items.required' => 'Minimal harus ada satu jasa potong dalam kelompok.',
            'configurations.*.items.*.cutting_id.required' => 'Jasa potong wajib dipilih.',
            'configurations.*.items.*.prices.required' => 'Rincian tier harga wajib diisi.',
        ]);

        $tierDefinitions = [
            'tier_1' => ['min' => 1, 'max' => 1, 'label' => '1 lbr'],
            'tier_2_5' => ['min' => 2, 'max' => 5, 'label' => '2-5 lbr'],
            'tier_6_50' => ['min' => 6, 'max' => 50, 'label' => '6-50 lbr'],
            'tier_100' => ['min' => 51, 'max' => 100, 'label' => '100 lbr'],
            'tier_300' => ['min' => 101, 'max' => 300, 'label' => '300 lbr'],
            'tier_500' => ['min' => 301, 'max' => 500, 'label' => '500 lbr'],
        ];
        $tierKeys = array_keys($tierDefinitions);
        $maximumAmount = 9999999999.99;
        $userName = Auth::user()->name ?? 'System';

        try {
            DB::transaction(function () use ($request, $productionCost, $tierDefinitions, $tierKeys, $maximumAmount, $userName) {
                $header = DB::table('m_digital_cutting_production_costs')
                    ->where('id', $productionCost->id)
                    ->lockForUpdate()
                    ->first();

                abort_unless($header, 404);

                $savedCuttingIds = DB::table('m_digital_cutting_production_cost_details')
                    ->where('digital_cutting_production_cost_id', $header->id)
                    ->pluck('cutting_id');

                $cuttings = DB::table('m_cuttings')
                    ->where(function ($query) use ($savedCuttingIds) {
                        $query->where('status', 'Active')
                            ->orWhereIn('id', $savedCuttingIds);
                    })
                    ->get()
                    ->keyBy('id');

                $categories = DB::table('m_categories')->get()->keyBy('id');

                $usedKeys = [];

                foreach ($request->input('configurations') as $confIndex => $configuration) {
                    $prefix = 'configurations.' . $confIndex;
                    $categoryIds = array_map('intval', $configuration['category_ids']);

                    if (count($categoryIds) !== count(array_unique($categoryIds))) {
                        throw ValidationException::withMessages([
                            $prefix . '.category_ids' => 'Kategori kertas tidak boleh dipilih lebih dari satu kali dalam satu kelompok.'
                        ]);
                    }

                    $blockCuttingIds = [];
                    foreach ($configuration['items'] as $itemIndex => $item) {
                        $itemPrefix = $prefix . '.items.' . $itemIndex;
                        $cuttingId = (int) ($item['cutting_id'] ?? 0);
                        $cutting = $cuttings->get($cuttingId);

                        if (!$cutting) {
                            throw ValidationException::withMessages([
                                $itemPrefix . '.cutting_id' => 'Jasa potong tidak valid atau tidak aktif.'
                            ]);
                        }

                        if (in_array($cuttingId, $blockCuttingIds, true)) {
                            throw ValidationException::withMessages([
                                $itemPrefix . '.cutting_id' => 'Jasa potong "' . $cutting->name . '" duplikat dalam satu kelompok.'
                            ]);
                        }
                        $blockCuttingIds[] = $cuttingId;

                        foreach ($tierKeys as $tierKey) {
                            $tierLabel = $tierDefinitions[$tierKey]['label'];
                            $rawPrice = $item['prices']['general'][$tierKey] ?? null;

                            $priceVal = $this->parseMoney($rawPrice);
                            if ($priceVal === null) {
                                throw ValidationException::withMessages([
                                    $itemPrefix . '.prices.general.' . $tierKey =>
                                    'Harga Umum (' . $tierLabel . ') pada jasa potong "' . $cutting->name . '" wajib diisi.'
                                ]);
                            }

                            if ($priceVal < 0 || $priceVal > $maximumAmount) {
                                throw ValidationException::withMessages([
                                    $itemPrefix . '.prices.general.' . $tierKey =>
                                    'Harga Umum (' . $tierLabel . ') pada jasa potong "' . $cutting->name . '" harus antara 0 dan 9.999.999.999,99.'
                                ]);
                            }
                        }

                        foreach ($categoryIds as $catId) {
                            $key = $catId . '|' . $cuttingId;
                            if (isset($usedKeys[$key])) {
                                $catName = $categories->get($catId)->name ?? 'Kategori #' . $catId;
                                throw ValidationException::withMessages([
                                    $itemPrefix . '.cutting_id' =>
                                    'Ongkos produksi jasa potong "' . $cutting->name . '" untuk kategori "' .
                                    $catName . '" duplikat dalam formulir.'
                                ]);
                            }
                            $usedKeys[$key] = true;
                        }
                    }
                }

                // Hapus rincian lama (FK onDelete cascade otomatis menghapus prices)
                DB::table('m_digital_cutting_production_cost_details')
                    ->where('digital_cutting_production_cost_id', $header->id)
                    ->delete();

                // Update audit header
                $audit = ['updated_by' => $userName, 'updated_at' => now()];
                DB::table('m_digital_cutting_production_costs')
                    ->where('id', $header->id)
                    ->update($audit);

                // Insert rincian detail dan harga baru
                foreach ($request->input('configurations') as $configuration) {
                    $categoryIds = array_map('intval', $configuration['category_ids']);

                    foreach ($categoryIds as $categoryId) {
                        foreach ($configuration['items'] as $item) {
                            $cuttingId = (int) $item['cutting_id'];

                            $detailId = DB::table('m_digital_cutting_production_cost_details')->insertGetId([
                                'digital_cutting_production_cost_id' => $header->id,
                                'cutting_id' => $cuttingId,
                                'category_id' => $categoryId,
                                'created_by' => $userName,
                                'created_at' => now(),
                            ] + $audit);

                            $priceRows = [];
                            foreach ($tierKeys as $tierKey) {
                                $rawGeneralPrice = $item['prices']['general'][$tierKey] ?? null;
                                $generalPrice = (float) $this->parseMoney($rawGeneralPrice);
                                $divisionPrice = round($generalPrice * 0.8, 2);

                                // Row General
                                $priceRows[] = [
                                    'digital_cutting_production_cost_detail_id' => $detailId,
                                    'price_type' => 'general',
                                    'quantity_tier' => $tierKey,
                                    'minimum_quantity' => $tierDefinitions[$tierKey]['min'],
                                    'maximum_quantity' => $tierDefinitions[$tierKey]['max'],
                                    'price' => $generalPrice,
                                    'created_by' => $userName,
                                    'created_at' => now(),
                                ] + $audit;

                                // Row Division (80%)
                                $priceRows[] = [
                                    'digital_cutting_production_cost_detail_id' => $detailId,
                                    'price_type' => 'division',
                                    'quantity_tier' => $tierKey,
                                    'minimum_quantity' => $tierDefinitions[$tierKey]['min'],
                                    'maximum_quantity' => $tierDefinitions[$tierKey]['max'],
                                    'price' => $divisionPrice,
                                    'created_by' => $userName,
                                    'created_at' => now(),
                                ] + $audit;
                            }

                            DB::table('m_digital_cutting_production_cost_prices')->insert($priceRows);
                        }
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Ongkos produksi jasa potong berhasil diperbarui.'
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat memperbarui ongkos produksi jasa potong.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $productionCost = $this->findProductionCost($id);

        if (!$productionCost) {
            return response()->json([
                'success' => false,
                'message' => 'Data ongkos produksi jasa potong tidak ditemukan.'
            ], 404);
        }

        try {
            return DB::transaction(function () use ($productionCost) {
                // Lock baris header yang akan dihapus
                $header = DB::table('m_digital_cutting_production_costs')
                    ->where('id', $productionCost->id)
                    ->lockForUpdate()
                    ->first();

                if (!$header) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data ongkos produksi jasa potong tidak ditemukan.'
                    ], 404);
                }

                // Hapus secara eksplisit rincian detail & tier harga yang hanya berelasi dengan header ini
                // Master data mesin, lokasi, vendor, kategori, dan master jasa potong tidak akan tersentuh
                $detailIds = DB::table('m_digital_cutting_production_cost_details')
                    ->where('digital_cutting_production_cost_id', $header->id)
                    ->pluck('id');

                if ($detailIds->isNotEmpty()) {
                    DB::table('m_digital_cutting_production_cost_prices')
                        ->whereIn('digital_cutting_production_cost_detail_id', $detailIds)
                        ->delete();

                    DB::table('m_digital_cutting_production_cost_details')
                        ->where('digital_cutting_production_cost_id', $header->id)
                        ->delete();
                }

                $deleted = DB::table('m_digital_cutting_production_costs')
                    ->where('id', $header->id)
                    ->delete();

                if ($deleted !== 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ongkos produksi jasa potong gagal dihapus.'
                    ], 409);
                }

                $scopeLabel = $productionCost->location_name .
                    ($productionCost->vendor_name ? ' - ' . $productionCost->vendor_name : '');

                return response()->json([
                    'success' => true,
                    'message' => 'Ongkos produksi jasa potong lokasi ' . $scopeLabel . ' berhasil dihapus.'
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data ongkos produksi jasa potong.'
            ], 500);
        }
    }

    public function dataPotong(Request $request)
    {
        $query = DB::table('m_digital_potong_production_costs as pc')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->leftJoin('m_digital_potong_production_cost_categories as d', 'd.digital_potong_production_cost_id', '=', 'pc.id')
            ->select(
                'pc.id as digital_potong_production_cost_id',
                'e.id as engine_id',
                'e.name as engine_name',
                'l.name as location_names',
                'v.name as vendor_name',
                DB::raw('COUNT(DISTINCT d.category_id) as category_count')
            )
            ->groupBy('pc.id', 'e.id', 'e.name', 'l.name', 'v.name');

        $totalRecords = (clone $query)->get()->count();
        $search = $request->input('search.value');

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(e.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(l.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(v.name) LIKE ?', [$search])
                    ->orWhereExists(function ($categories) use ($search) {
                        $categories->selectRaw('1')
                            ->from('m_digital_potong_production_cost_categories as cd')
                            ->join('m_categories as c', 'c.id', '=', 'cd.category_id')
                            ->whereColumn('cd.digital_potong_production_cost_id', 'pc.id')
                            ->whereRaw('LOWER(c.name) LIKE ?', [$search]);
                    });
            });
        }

        $totalFiltered = (clone $query)->get()->count();
        $data = $query
            ->orderBy('e.name')
            ->orderBy('l.name')
            ->orderBy('v.name')
            ->orderBy('pc.id')
            ->offset((int) $request->input('start', 0))
            ->limit((int) $request->input('length', 10))
            ->get();

        $categoryNames = DB::table('m_digital_potong_production_cost_categories as d')
            ->join('m_categories as c', 'c.id', '=', 'd.category_id')
            ->whereIn('d.digital_potong_production_cost_id', $data->pluck('digital_potong_production_cost_id'))
            ->select('d.digital_potong_production_cost_id', 'c.id as category_id', 'c.name')
            ->distinct()
            ->orderBy('c.name')
            ->get()
            ->groupBy('digital_potong_production_cost_id');

        $data->each(function ($row) use ($categoryNames) {
            $row->category_count = $categoryNames
                ->get($row->digital_potong_production_cost_id, collect())
                ->count();
            $row->category_names = $categoryNames
                ->get($row->digital_potong_production_cost_id, collect())
                ->pluck('name')
                ->implode(', ');
            $row->is_potong = true;
            $row->cutting_spec = 'A3, A4, A5, <=A6';
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function destroyPotong($id)
    {
        $decodedId = base64_decode($id, true);
        if ($decodedId === false || !ctype_digit($decodedId) || (int) $decodedId <= 0) {
            $decodedId = (int) $id;
        }

        $header = DB::table('m_digital_potong_production_costs as pc')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->where('pc.id', $decodedId)
            ->select('pc.*', 'l.name as location_name', 'v.name as vendor_name')
            ->first();

        if (!$header) {
            return response()->json([
                'success' => false,
                'message' => 'Data ongkos produksi potong tidak ditemukan.'
            ], 404);
        }

        try {
            return DB::transaction(function () use ($header) {
                // Lock header yang akan dihapus
                $lockedHeader = DB::table('m_digital_potong_production_costs')
                    ->where('id', $header->id)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedHeader) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data ongkos produksi potong tidak ditemukan.'
                    ], 404);
                }

                $categoryIds = DB::table('m_digital_potong_production_cost_categories')
                    ->where('digital_potong_production_cost_id', $header->id)
                    ->pluck('id');

                if ($categoryIds->isNotEmpty()) {
                    DB::table('m_digital_potong_production_cost_prices')
                        ->whereIn('digital_potong_production_cost_category_id', $categoryIds)
                        ->delete();

                    DB::table('m_digital_potong_production_cost_categories')
                        ->where('digital_potong_production_cost_id', $header->id)
                        ->delete();
                }

                $deleted = DB::table('m_digital_potong_production_costs')
                    ->where('id', $header->id)
                    ->delete();

                if ($deleted !== 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ongkos produksi potong gagal dihapus.'
                    ], 409);
                }

                $scopeLabel = $header->location_name .
                    ($header->vendor_name ? ' - ' . $header->vendor_name : '');

                return response()->json([
                    'success' => true,
                    'message' => 'Ongkos produksi potong lokasi ' . $scopeLabel . ' berhasil dihapus.'
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data ongkos produksi potong.'
            ], 500);
        }
    }

    public function createPotong()
    {
        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['categories'] = DB::table('m_categories')
            ->orderBy('name')
            ->get();

        $data['cuttings'] = DB::table('m_cuttings')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['vendors'] = DB::table('m_vendors')
            ->where('status', 'Active')
            ->get();

        $data['default_cutting_type'] = 'potong';

        return view(
            'admin.production-cost-finishing-cutting.create'
        )->with($data);
    }

    public function storePotong(Request $request)
    {
        return $this->store($request);
    }

    private function findPotongProductionCost($id)
    {
        $decodedId = base64_decode($id, true);
        if ($decodedId === false || !ctype_digit($decodedId) || (int) $decodedId <= 0) {
            $decodedId = (int) $id;
        }

        return DB::table('m_digital_potong_production_costs as pc')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->where('pc.id', $decodedId)
            ->select('pc.*', 'e.name as engine_name', 'l.name as location_name', 'v.name as vendor_name')
            ->first();
    }

    public function editPotong($id)
    {
        $productionCost = $this->findPotongProductionCost($id);
        abort_unless($productionCost, 404);

        $categoriesData = DB::table('m_digital_potong_production_cost_categories as c')
            ->where('c.digital_potong_production_cost_id', $productionCost->id)
            ->orderBy('c.category_id')
            ->get();

        $prices = DB::table('m_digital_potong_production_cost_prices')
            ->whereIn('digital_potong_production_cost_category_id', $categoriesData->pluck('id'))
            ->get()
            ->groupBy('digital_potong_production_cost_category_id');

        $sizes = ['A3', 'A4', 'A5', '<=A6'];

        // Group categories that have identical prices
        $groups = $categoriesData->map(function ($cat) use ($prices, $sizes) {
            $catPrices = $prices->get($cat->id, collect());
            $generalPrices = $catPrices->where('price_type', 'general')->keyBy('size_class');
            $divisionPrices = $catPrices->where('price_type', 'division')->keyBy('size_class');

            $itemPrices = [
                'general' => [],
                'division' => [],
            ];

            foreach ($sizes as $size) {
                $gen = $generalPrices->get($size);
                $div = $divisionPrices->get($size);

                $itemPrices['general'][$size] = $gen ? number_format((float) $gen->min_price, 0, ',', '.') : '';
                $itemPrices['division'][$size] = $div ? number_format((float) $div->min_price, 0, ',', '.') : '';
            }

            return [
                'category_ids' => [(int) $cat->category_id],
                'prices' => $itemPrices,
            ];
        })
        ->groupBy(fn($group) => json_encode($group['prices']))
        ->map(fn($sameGroups) => [
            'category_ids' => $sameGroups->pluck('category_ids')->flatten()->values()->all(),
            'prices' => $sameGroups->first()['prices'],
        ])
        ->values();

        $categories = DB::table('m_categories')
            ->orderBy('name')
            ->get();

        return view('admin.production-cost-finishing-cutting.edit-potong', compact(
            'productionCost',
            'groups',
            'categories'
        ));
    }

    public function updatePotong(Request $request, $id)
    {
        $productionCost = $this->findPotongProductionCost($id);
        abort_unless($productionCost, 404);

        $request->validate([
            'configurations' => 'required|array|min:1',
            'configurations.*' => 'required|array',
            'configurations.*.category_ids' => 'required|array|min:1',
            'configurations.*.category_ids.*' => 'required|integer|exists:m_categories,id',
            'configurations.*.prices' => 'required|array',
            'configurations.*.prices.general' => 'required|array',
            'configurations.*.prices.division' => 'required|array',
        ], [
            'configurations.required' => 'Minimal harus ada 1 kelompok konfigurasi.',
            'configurations.*.category_ids.required' => 'Minimal pilih satu kategori kertas/bahan dalam kelompok.',
            'configurations.*.prices.required' => 'Rincian harga wajib diisi.',
        ]);

        $potongSizes = ['A3', 'A4', 'A5', '<=A6'];
        $potongTypes = [
            'general' => 'Harga Umum',
            'division' => 'Harga Divisi'
        ];
        $maximumAmount = 9999999999.99;
        $userName = Auth::user()->name ?? 'System';

        try {
            DB::transaction(function () use ($request, $productionCost, $potongSizes, $potongTypes, $maximumAmount, $userName) {
                $header = DB::table('m_digital_potong_production_costs')
                    ->where('id', $productionCost->id)
                    ->lockForUpdate()
                    ->first();

                abort_unless($header, 404);

                $categories = DB::table('m_categories')->get()->keyBy('id');
                $usedCategories = [];

                foreach ($request->input('configurations') as $confIndex => $configuration) {
                    $prefix = 'configurations.' . $confIndex;
                    $categoryIds = array_map('intval', $configuration['category_ids']);

                    if (count($categoryIds) !== count(array_unique($categoryIds))) {
                        throw ValidationException::withMessages([
                            $prefix . '.category_ids' => 'Kategori kertas tidak boleh dipilih lebih dari satu kali dalam satu kelompok.'
                        ]);
                    }

                    foreach ($categoryIds as $catId) {
                        if (isset($usedCategories[$catId])) {
                            $catName = $categories->get($catId)->name ?? 'Kategori #' . $catId;
                            throw ValidationException::withMessages([
                                $prefix . '.category_ids' => 'Kategori "' . $catName . '" duplikat, sudah dipilih pada kelompok lain.'
                            ]);
                        }
                        $usedCategories[$catId] = true;
                    }

                    foreach ($potongTypes as $pType => $pTypeLabel) {
                        foreach ($potongSizes as $pSize) {
                            $rawPrice = $configuration['prices'][$pType][$pSize] ?? null;
                            $priceVal = $this->parseMoney($rawPrice);

                            if ($priceVal === null) {
                                throw ValidationException::withMessages([
                                    $prefix . '.prices.' . $pType . '.' . $pSize =>
                                    $pTypeLabel . ' ukuran ' . $pSize . ' pada kelompok #' . ($confIndex + 1) . ' wajib diisi.'
                                ]);
                            }

                            if ($priceVal < 0 || $priceVal > $maximumAmount) {
                                throw ValidationException::withMessages([
                                    $prefix . '.prices.' . $pType . '.' . $pSize =>
                                    $pTypeLabel . ' ukuran ' . $pSize . ' harus antara 0 dan 9.999.999.999,99.'
                                ]);
                            }
                        }
                    }
                }

                // Delete existing categories and prices
                $oldCategoryIds = DB::table('m_digital_potong_production_cost_categories')
                    ->where('digital_potong_production_cost_id', $header->id)
                    ->pluck('id');

                if ($oldCategoryIds->isNotEmpty()) {
                    DB::table('m_digital_potong_production_cost_prices')
                        ->whereIn('digital_potong_production_cost_category_id', $oldCategoryIds)
                        ->delete();

                    DB::table('m_digital_potong_production_cost_categories')
                        ->where('digital_potong_production_cost_id', $header->id)
                        ->delete();
                }

                // Update audit header
                $audit = ['updated_by' => $userName, 'updated_at' => now()];
                DB::table('m_digital_potong_production_costs')
                    ->where('id', $header->id)
                    ->update($audit);

                // Insert new categories and prices
                foreach ($request->input('configurations') as $configuration) {
                    $categoryIds = array_map('intval', $configuration['category_ids']);

                    foreach ($categoryIds as $categoryId) {
                        $catId = DB::table('m_digital_potong_production_cost_categories')->insertGetId([
                            'digital_potong_production_cost_id' => $header->id,
                            'category_id' => $categoryId,
                            'created_by' => $userName,
                            'created_at' => now(),
                        ] + $audit);

                        $priceRows = [];
                        foreach ($potongTypes as $pType => $pTypeLabel) {
                            foreach ($potongSizes as $pSize) {
                                $rawPrice = $configuration['prices'][$pType][$pSize] ?? null;
                                $priceVal = (float) $this->parseMoney($rawPrice);
                                $priceRows[] = [
                                    'digital_potong_production_cost_category_id' => $catId,
                                    'size_class' => $pSize,
                                    'price_type' => $pType,
                                    'min_price' => $priceVal,
                                    'created_by' => $userName,
                                    'created_at' => now(),
                                    'updated_by' => $userName,
                                    'updated_at' => now(),
                                ];
                            }
                        }
                        DB::table('m_digital_potong_production_cost_prices')->insert($priceRows);
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Ongkos produksi potong berhasil diperbarui.'
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat memperbarui ongkos produksi potong.'
            ], 500);
        }
    }

    public function detailsPotong($id)
    {
        $productionCost = $this->findPotongProductionCost($id);
        abort_unless($productionCost, 404);

        $categoriesData = DB::table('m_digital_potong_production_cost_categories as c')
            ->join('m_categories as cat', 'cat.id', '=', 'c.category_id')
            ->where('c.digital_potong_production_cost_id', $productionCost->id)
            ->select('c.*', 'cat.name as category_name')
            ->orderBy('cat.name')
            ->get();

        $prices = DB::table('m_digital_potong_production_cost_prices')
            ->whereIn('digital_potong_production_cost_category_id', $categoriesData->pluck('id'))
            ->get()
            ->groupBy('digital_potong_production_cost_category_id');

        $sizes = [
            'A3' => 'A3',
            'A4' => 'A4',
            'A5' => 'A5',
            '<=A6' => '≤ A6'
        ];

        $priceGroups = [
            'general' => 'Harga Umum',
            'division' => 'Harga Divisi',
        ];

        $categoriesData->each(function ($cat) use ($prices, $priceGroups, $sizes) {
            $catPrices = ($prices->get($cat->id) ?? collect())->keyBy(
                fn($p) => $p->price_type . '|' . $p->size_class
            );
            $cat->prices = [];
            foreach (array_keys($priceGroups) as $groupKey) {
                foreach (array_keys($sizes) as $sizeKey) {
                    $cat->prices[$groupKey][$sizeKey] = $catPrices->get($groupKey . '|' . $sizeKey);
                }
            }
        });

        $groupedCategories = $categoriesData->groupBy(function ($cat) {
            $flat = [];
            foreach ($cat->prices as $gk => $sizePrices) {
                foreach ($sizePrices as $sk => $pObj) {
                    $flat[] = $gk . ':' . $sk . ':' . ($pObj ? $pObj->min_price : '0');
                }
            }
            return implode(',', $flat);
        })->map(function ($sameCats) {
            $first = $sameCats->first();
            return (object) [
                'category_names' => $sameCats->pluck('category_name')->values(),
                'prices' => $first->prices,
            ];
        })->values();

        return view('admin.production-cost-finishing-cutting.details-potong', compact(
            'productionCost',
            'groupedCategories',
            'sizes',
            'priceGroups'
        ));
    }
}
