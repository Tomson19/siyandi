<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WilayahController extends Controller
{
    private const API_BASE = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    // mapping nama kabupaten (di DB) -> regency_id API emsifa
    private const KABUPATEN_TO_REGENCY_ID = [
        'KAMPAR'             => '1401',
        'INDRAGIRI HULU'     => '1402',
        'INDRAGIRI HILIR'    => '1403',
        'PELALAWAN'          => '1404',
        'SIAK'               => '1405',
        'KUANTAN SINGINGI'   => '1406',
        'ROKAN HULU'         => '1407',
        'BENGKALIS'          => '1408',
        'ROKAN HILIR'        => '1409',
        'KEPULAUAN MERANTI'  => '1410',
        'PEKANBARU'          => '1471',
        'DUMAI'              => '1473',
    ];

    /**
     * GET /wilayah/kecamatan?kabupaten=Kepulauan Meranti
     */
    public function kecamatan(Request $request)
    {
        $kabupaten = strtoupper($request->query('kabupaten', ''));

        $regencyId = null;
        foreach (self::KABUPATEN_TO_REGENCY_ID as $key => $id) {
            if (str_contains($kabupaten, $key)) {
                $regencyId = $id;
                break;
            }
        }

        if (!$regencyId) {
            return response()->json([
                'message' => 'Kabupaten tidak dikenali',
            ], 404);
        }

        $response = Http::get(self::API_BASE . "/districts/{$regencyId}.json");

        if (!$response->ok()) {
            return response()->json([
                'message' => 'Gagal ambil data kecamatan',
            ], $response->status());
        }

        // balikin JSON mentah dari API
        return $response->json();
    }

    /**
     * GET /wilayah/desa/{districtId}
     */
    public function desa(string $districtId)
    {
        $response = Http::get(self::API_BASE . "/villages/{$districtId}.json");

        if (!$response->ok()) {
            return response()->json([
                'message' => 'Gagal ambil data desa',
            ], $response->status());
        }

        return $response->json();
    }
}
