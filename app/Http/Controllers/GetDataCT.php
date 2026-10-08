<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetDataCT extends Controller
{
    public function getMaterialSizes(Request $request)
    {
        $id = $request->id;

        $materialSizes = DB::table('m_material_sizes as a')
            ->leftJoin(
                'm_materials as b',
                'a.material_id',
                '=',
                'b.id'
            )
            ->where('b.id', $id)
            ->select(
                'a.id',
                'a.width',
                'a.unit'
            )
            ->orderBy('a.width')
            ->get();

        return response()->json($materialSizes);
    }

    public function getLaminationSizes(Request $request)
    {
        $id = $request->id;

        $laminationSizes = DB::table('m_lamination_sizes as a')
            ->leftJoin(
                'm_laminations as b',
                'a.lamination_id',
                '=',
                'b.id'
            )
            ->where('b.id', $id)
            ->select(
                'a.id',
                'a.width',
                'a.unit'
            )
            ->orderBy('a.width')
            ->get();

        return response()->json($laminationSizes);
    }
}
