<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function getAllRegions()
    {
        $response = Http::get('https://gisapis.manpits.xyz/api/mregion');
        return $response->json();
    }

    public function getProvinceById($id)
    {
        $response = Http::get("https://gisapis.manpits.xyz/api/provinsi/{$id}");
        return $response->json();
    }

    public function getKabupatenByProvinceId($id)
    {
        $response = Http::get("https://gisapis.manpits.xyz/api/kabupaten/{$id}");
        return $response->json();
    }

    public function getKecamatanByKabupatenId($id)
    {
        $response = Http::get("https://gisapis.manpits.xyz/api/kecamatan/{$id}");
        return $response->json();
    }

    public function getDesaByKecamatanId($id)
    {
        $response = Http::get("https://gisapis.manpits.xyz/api/desa/{$id}");
        return $response->json();
    }


}

