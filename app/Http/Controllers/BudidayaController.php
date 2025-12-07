<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BudidayaController extends Controller
{
    public function index()
    {
        
        $shpUrl = asset('mapdata/FINAL_POTENSI_BUDIDAYA_KOPI_OK_KAB.zip');

        
        $fieldMap = [
            'komoditi' => 'KOMODITI',
            'kabupaten'=> 'KABUPATEN',
            'luas'     => 'Luas_Ha', 
        ];

        return view('budidaya.index', compact('shpUrl', 'fieldMap'));
    }
}
