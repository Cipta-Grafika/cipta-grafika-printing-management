<?php

namespace App\Support;

class A3PlusImposition
{
    public const PRINTABLE_LENGTH_CM = 47.5;
    public const PRINTABLE_WIDTH_CM = 31.5;

    public const DIE_CUT_GAP_CM = 0.5;   // 5mm
    public const KISS_CUT_GAP_CM = 0.2;  // 2mm
    public const EPSILON = 1e-9;

    /**
     * Resolve the inter-piece spacing (in cm) for an A3+ cutting option.
     */
    public static function gapCmFromName(?string $cuttingNameOrAlias): float
    {
        $normalized = strtolower(trim((string) $cuttingNameOrAlias));

        if ($normalized === '' || $normalized === 'none' || $normalized === 'tanpa cutting' || $normalized === 'tanpa cutting / tanpa potong') {
            return 0.0;
        }

        if (str_contains($normalized, 'die')) {
            return self::DIE_CUT_GAP_CM;
        }

        if (str_contains($normalized, 'kiss')) {
            return self::KISS_CUT_GAP_CM;
        }

        // Perforasi (porforasi) dan potong lurus tidak memerlukan jarak antar pcs.
        if (str_contains($normalized, 'perforasi') || str_contains($normalized, 'porforasi') || str_contains($normalized, 'potong')) {
            return 0.0;
        }

        return 0.0;
    }

    /**
     * Resolve the inter-piece spacing in whole millimeters.
     */
    public static function gapMmFromName(?string $cuttingNameOrAlias): int
    {
        return (int) round(self::gapCmFromName($cuttingNameOrAlias) * 10);
    }

    /**
     * Calculate A3+ layout imposition where n pieces require (n - 1) inter-piece gaps:
     * n * pieceSize + (n - 1) * gap <= printableAxis  =>  floor((printableAxis + gap) / (pieceSize + gap)).
     *
     * @return array{up_normal: int, up_rotated: int, up_count: int, raw_up_count: int, orientation: string, gap_cm: float, gap_mm: int}
     */
    public static function calculate(
        float $productLength,
        float $productWidth,
        float $gapCm = 0.0,
        float $sheetLength = self::PRINTABLE_LENGTH_CM,
        float $sheetWidth = self::PRINTABLE_WIDTH_CM
    ): array {
        $gapCm = max(0.0, $gapCm);

        if ($productLength <= 0 || $productWidth <= 0) {
            return [
                'up_normal' => 0,
                'up_rotated' => 0,
                'up_count' => 1,
                'raw_up_count' => 0,
                'orientation' => 'Normal',
                'gap_cm' => $gapCm,
                'gap_mm' => (int) round($gapCm * 10),
            ];
        }

        $fitAxis = static function (float $printableAxis, float $pieceDim) use ($gapCm): int {
            if ($pieceDim <= 0 || $pieceDim > ($printableAxis + self::EPSILON)) {
                return 0;
            }

            return max(0, (int) floor((($printableAxis + $gapCm) / ($pieceDim + $gapCm)) + self::EPSILON));
        };

        $upNormal = $fitAxis($sheetLength, $productLength) * $fitAxis($sheetWidth, $productWidth);
        $upRotated = $fitAxis($sheetLength, $productWidth) * $fitAxis($sheetWidth, $productLength);
        $rawUp = max($upNormal, $upRotated);
        $upCount = max(1, $rawUp);
        $orientation = ($upRotated > $upNormal) ? 'Rotated' : 'Normal';

        return [
            'up_normal' => $upNormal,
            'up_rotated' => $upRotated,
            'up_count' => $upCount,
            'raw_up_count' => $rawUp,
            'orientation' => $orientation,
            'gap_cm' => $gapCm,
            'gap_mm' => (int) round($gapCm * 10),
        ];
    }
}
