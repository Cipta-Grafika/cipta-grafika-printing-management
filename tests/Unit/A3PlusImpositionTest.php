<?php

namespace Tests\Unit;

use App\Support\A3PlusImposition;
use PHPUnit\Framework\TestCase;

class A3PlusImpositionTest extends TestCase
{
    public function test_it_resolves_cutting_gaps_correctly(): void
    {
        $this->assertSame(0.5, A3PlusImposition::gapCmFromName('Die Cut'));
        $this->assertSame(5, A3PlusImposition::gapMmFromName('die'));
        $this->assertSame(0.2, A3PlusImposition::gapCmFromName('Kiss Cut'));
        $this->assertSame(2, A3PlusImposition::gapMmFromName('kiss'));
        $this->assertSame(0.0, A3PlusImposition::gapCmFromName('Perforasi'));
        $this->assertSame(0.0, A3PlusImposition::gapCmFromName('porforasi'));
        $this->assertSame(0.0, A3PlusImposition::gapCmFromName('Potong'));
        $this->assertSame(0.0, A3PlusImposition::gapCmFromName('potong'));
        $this->assertSame(0.0, A3PlusImposition::gapCmFromName('none'));
        $this->assertSame(0.0, A3PlusImposition::gapCmFromName(null));
    }

    public function test_baseline_without_cutting_keeps_existing_counts(): void
    {
        $res = A3PlusImposition::calculate(9.0, 5.5, 0.0);

        $this->assertSame(25, $res['up_count']);
        $this->assertSame('Normal', $res['orientation']);
        $this->assertSame(0, $res['gap_mm']);
    }

    public function test_die_cut_and_kiss_cut_apply_inter_piece_gaps(): void
    {
        $none = A3PlusImposition::calculate(5.0, 5.0, 0.0);
        $kiss = A3PlusImposition::calculate(5.0, 5.0, A3PlusImposition::KISS_CUT_GAP_CM);
        $die = A3PlusImposition::calculate(5.0, 5.0, A3PlusImposition::DIE_CUT_GAP_CM);

        // 5x5 on 47.5x31.5: 9*5 + 8*0.2 = 46.6 <= 47.5 and 6*5 + 5*0.2 = 31.0 <= 31.5 => 54 pcs
        $this->assertSame(54, $none['up_count']);
        $this->assertSame(54, $kiss['up_count']);
        // Die cut 5mm: 8*5 + 7*0.5 = 43.5 <= 47.5 and 5*5 + 4*0.5 = 27.0 <= 31.5 => 40 pcs
        $this->assertSame(40, $die['up_count']);

        // Edge case: 7.5cm x 7.5cm with 5mm die cut -> 4 * 7.5 + 3 * 0.5 = 31.5cm exact fit along width
        $exactEdge = A3PlusImposition::calculate(7.5, 7.5, A3PlusImposition::DIE_CUT_GAP_CM);
        $this->assertSame(24, $exactEdge['up_count']); // 6 x 4 = 24

        // IEEE-754 float boundary: 3*15.65 + 2*0.2 = 47.35 <= 47.5 and 2*15.65 + 1*0.2 = 31.5 <= 31.5 => 3 x 2 = 6
        $floatBoundary = A3PlusImposition::calculate(15.65, 15.65, A3PlusImposition::KISS_CUT_GAP_CM);
        $this->assertSame(6, $floatBoundary['up_count']); // 3 x 2 = 6
    }

    public function test_oversized_piece_safely_reports_zero_raw_up(): void
    {
        $oversized = A3PlusImposition::calculate(50.0, 10.0, 0.5);

        $this->assertSame(0, $oversized['raw_up_count']);
        $this->assertSame(1, $oversized['up_count']);
    }
}
