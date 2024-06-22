<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Polyline;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;


class PolylineController extends Controller
{
    public function index()
    {
        $token = session('token');
        $userId = session('user_id');
    
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/ruasjalan?user_id=' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $polylines = json_decode($response->getBody(), true);
    
            // Inisialisasi variabel untuk menyimpan jumlah kondisi jalan
            $jalan_baik = 0;
            $jalan_sedang = 0;
            $jalan_rusak = 0;
    
        if (isset($polylines['ruasjalan']) && is_array($polylines['ruasjalan'])) {
            foreach ($polylines['ruasjalan'] as $polyline) {
                switch ($polyline['kondisi_id']) {
                    case 1:
                        $jalan_baik++;
                        break;
                    case 2:
                        $jalan_sedang++;
                        break;
                    case 3:
                        $jalan_rusak++;
                        break;
                    default:
                        // do nothing or handle other cases
                        break;
                }
            }
        } else {
            Log::warning('No ruasjalan data or invalid format:', (array)$polylines);
        }
        
        if ($response->getStatusCode() == 200) {
            $polylines = json_decode($response->getBody(), true);
            
            if (isset($polylines['ruasjalan']) && is_array($polylines['ruasjalan'])) {
                foreach ($polylines['ruasjalan'] as &$polyline) {
                    $polyline['paths'] = $polyline['paths'];
                }
            } else {
                Log::warning('No ruasjalan data or invalid format:', (array)$polylines);
            }
        
            return view('polyline.index', compact('polylines'));
        } else {
            return redirect()->back()->with('error', 'Failed to fetch data from API');
        }
    }
}

    public function create()
    {
        return view('polyline.create');
    }

public function detail($id)
    {
        $token = session('token');
        $client = new Client();

        // try {
            // Permintaan untuk mendapatkan data ruas jalan berdasarkan ID
            $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/ruasjalan/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() == 200) {
                $data_ruas_jalan = json_decode($response->getBody(), true);
                $ruasJalan = $data_ruas_jalan['ruasjalan'];

                // Permintaan untuk mendapatkan data tambahan
                $regionResponse = $client->request('GET', 'https://gisapis.manpits.xyz/api/mregion', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]);
                $eksistingResponse = $client->request('GET', 'https://gisapis.manpits.xyz/api/meksisting', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]);
                $jenisjalanResponse = $client->request('GET', 'https://gisapis.manpits.xyz/api/mjenisjalan', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]);
                $kondisiResponse = $client->request('GET', 'https://gisapis.manpits.xyz/api/mkondisi', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]);

                if ($regionResponse->getStatusCode() == 200 &&          $eksistingResponse->getStatusCode() == 200 && $jenisjalanResponse->getStatusCode() == 200 && $kondisiResponse->getStatusCode() == 200) {
                    $regionData = json_decode($regionResponse->getBody(), true);
                    $eksistingData = json_decode($eksistingResponse->getBody(), true)['eksisting'];
                    $jenisjalanData = json_decode($jenisjalanResponse->getBody(), true)['eksisting'];
                    $kondisiData = json_decode($kondisiResponse->getBody(), true)['eksisting'];

                    // Mencari kecamatan_id, kabupaten_id, dan province_id
                    $desa_id = $ruasJalan['desa_id'];
                    $kecamatan_id = null;
                    $kabupaten_id = null;
                    $province_id = null;

                    foreach ($regionData['desa'] as $desa) {
                        if ($desa['id'] == $desa_id) {
                            $kecamatan_id = $desa['kec_id'];
                            break;
                        }
                    }

                    foreach ($regionData['kecamatan'] as $kecamatan) {
                        if ($kecamatan['id'] == $kecamatan_id) {
                            $kabupaten_id = $kecamatan['kab_id'];
                            break;
                        }
                    }

                    foreach ($regionData['kabupaten'] as $kabupaten) {
                        if ($kabupaten['id'] == $kabupaten_id) {
                            $province_id = $kabupaten['prov_id'];
                            break;
                        }
                    }

                    $ruasJalan['kecamatan_id'] = $kecamatan_id;
                    $ruasJalan['kabupaten_id'] = $kabupaten_id;
                    $ruasJalan['province_id'] = $province_id;


                    return view('polyline.detail', compact('ruasJalan', 'regionData', 'eksistingData', 'jenisjalanData', 'kondisiData'));
                } else {
                    return redirect()->route('polyline.index')->with('error', 'Failed to retrieve data from one or more sources');
                }
            } else {
                return redirect()->route('polyline.index')->with('error', 'Failed to retrieve ruas jalan data');
            }
        // } catch (\Exception $e) {
        //     return redirect()->route('polyline.edit', ['id' => $id])->with([
        //         'error' => 'An error occurred: ' . $e->getMessage(),
        //         'ruasjalan' => $ruasJalan, // Mengirim kembali data ruasjalan
        //         'regionData' => $regionData, // Mengirim kembali data tambahan
        //         'eksistingData' => $eksistingData, // Mengirim kembali data tambahan
        //         'jenisjalanData' => $jenisjalanData, // Mengirim kembali data tambahan
        //         'kondisiData' => $kondisiData, // Mengirim kembali data tambahan
        //     ]);
        // }
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'coordinates' => 'required|array',
            'coordinates.*.lat' => 'required|numeric',
            'coordinates.*.lng' => 'required|numeric',
        ]);
    
        $coordinates = $request->coordinates;
        foreach ($coordinates as $coordinate) {
            if (!isset($coordinate['lat']) || !isset($coordinate['lng'])) {
                return redirect()->back()->with('error', 'Format koordinat tidak valid.');
            }
        }
    
        $coordinates = array_map(function($coord) {
            return ['lat' => floatval($coord['lat']), 'lng' => floatval($coord['lng'])];
        }, $coordinates);
    
        $encodedCoordinates = $this->encodePolyline($coordinates);
    
        $client = new Client();
        $response = $client->request('POST', 'https://gisapis.manpits.xyz/api/ruasjalan', [
            'headers' => [
                'Authorization' => 'Bearer ' . session('token'),
                'Accept' => 'application/json',
            ],
            'json' => [
                'nama_ruas' => $request->name,
                'paths' => $encodedCoordinates,
            ],
        ]);
    
        if ($response->getStatusCode() == 201 || $response->getStatusCode() == 200) {
            return redirect()->route('polyline.index')->with('success', 'Polyline created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create polyline.');
        }
    }

    
    
    public function edit($id)
    {
        $token = session('token');
        $client = new Client();

        // try {
            // Permintaan untuk mendapatkan data ruas jalan berdasarkan ID
            $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/ruasjalan/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() == 200) {
                $data_ruas_jalan = json_decode($response->getBody(), true);
                $ruasJalan = $data_ruas_jalan['ruasjalan'];

                // Permintaan untuk mendapatkan data tambahan
                $regionResponse = $client->request('GET', 'https://gisapis.manpits.xyz/api/mregion', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]);
                $eksistingResponse = $client->request('GET', 'https://gisapis.manpits.xyz/api/meksisting', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]);
                $jenisjalanResponse = $client->request('GET', 'https://gisapis.manpits.xyz/api/mjenisjalan', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]);
                $kondisiResponse = $client->request('GET', 'https://gisapis.manpits.xyz/api/mkondisi', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]);

                if ($regionResponse->getStatusCode() == 200 &&          $eksistingResponse->getStatusCode() == 200 && $jenisjalanResponse->getStatusCode() == 200 && $kondisiResponse->getStatusCode() == 200) {
                    $regionData = json_decode($regionResponse->getBody(), true);
                    $eksistingData = json_decode($eksistingResponse->getBody(), true)['eksisting'];
                    $jenisjalanData = json_decode($jenisjalanResponse->getBody(), true)['eksisting'];
                    $kondisiData = json_decode($kondisiResponse->getBody(), true)['eksisting'];

                    // Mencari kecamatan_id, kabupaten_id, dan province_id
                    $desa_id = $ruasJalan['desa_id'];
                    $kecamatan_id = null;
                    $kabupaten_id = null;
                    $province_id = null;

                    foreach ($regionData['desa'] as $desa) {
                        if ($desa['id'] == $desa_id) {
                            $kecamatan_id = $desa['kec_id'];
                            break;
                        }
                    }

                    foreach ($regionData['kecamatan'] as $kecamatan) {
                        if ($kecamatan['id'] == $kecamatan_id) {
                            $kabupaten_id = $kecamatan['kab_id'];
                            break;
                        }
                    }

                    foreach ($regionData['kabupaten'] as $kabupaten) {
                        if ($kabupaten['id'] == $kabupaten_id) {
                            $province_id = $kabupaten['prov_id'];
                            break;
                        }
                    }

                    $ruasJalan['kecamatan_id'] = $kecamatan_id;
                    $ruasJalan['kabupaten_id'] = $kabupaten_id;
                    $ruasJalan['province_id'] = $province_id;


                    return view('polyline.edit', compact('ruasJalan', 'regionData', 'eksistingData', 'jenisjalanData', 'kondisiData'));
                } else {
                    return redirect()->route('polyline.index')->with('error', 'Failed to retrieve data from one or more sources');
                }
            } else {
                return redirect()->route('polyline.index')->with('error', 'Failed to retrieve ruas jalan data');
            }
        // } catch (\Exception $e) {
        //     return redirect()->route('polyline.edit', ['id' => $id])->with([
        //         'error' => 'An error occurred: ' . $e->getMessage(),
        //         'ruasjalan' => $ruasJalan, // Mengirim kembali data ruasjalan
        //         'regionData' => $regionData, // Mengirim kembali data tambahan
        //         'eksistingData' => $eksistingData, // Mengirim kembali data tambahan
        //         'jenisjalanData' => $jenisjalanData, // Mengirim kembali data tambahan
        //         'kondisiData' => $kondisiData, // Mengirim kembali data tambahan
        //     ]);
        // }
        
    }
        




    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ruas' => 'required|string|max:255',
            'paths' => 'required|string', // Ubah menjadi string karena latlng adalah string
            'desa_id' => 'required|numeric',
            'kode_ruas' => 'required|string|max:255',
            'panjang' => 'required|numeric',
            'lebar' => 'required|numeric',
            'eksisting_id' => 'required|numeric',
            'kondisi_id' => 'required|numeric',
            'jenisjalan_id' => 'required|numeric',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('PUT', 'https://gisapis.manpits.xyz/api/ruasjalan/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => $request->all(), // Kirim data yang sudah divalidasi langsung
            ]);

            if ($response->getStatusCode() == 200) {
                return redirect()->route('polyline.index')->with('success', 'Data ruas jalan berhasil diupdate.');
            } else {
                return redirect()->back()->with('error', 'Failed to update data.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    private function getDataRegion($token)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/mregion', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    
        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        } else {
            return [];
        }
    }
    
    public function destroy($id)
    {
        $token = session('token');
        $client = new Client();
        $response = $client->request('DELETE', 'https://gisapis.manpits.xyz/api/ruasjalan/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    
        if ($response->getStatusCode() == 200) {
            return redirect()->route('polyline.index')->with('success', 'Data polyline berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data polyline.');
        }
    }
    
    

    

    private function encodePolyline($coordinates)
    {
        $points = array_map(function($coord) {
            return [floatval($coord['lat']), floatval($coord['lng'])];
        }, $coordinates);
    
        return Polyline::encode($points);
    }
    
    private function decodePolyline($encodedString)
    {
        try {
            $points = Polyline::decode($encodedString);
            
            if (!is_array($points) || empty($points)) {
                Log::error('Failed to decode polyline or invalid format:', ['encodedString' => $encodedString, 'decodedPoints' => $points]);
                return [];
            }
            
            $latlngs = array_map(function($point) {
                if (!is_array($point) || count($point) < 2) {
                    Log::warning('Invalid point format:', ['point' => $point]);
                    return null;
                }
                return ['lat' => $point[0], 'lng' => $point[1]];
            }, $points);
            
            $latlngs = array_filter($latlngs);
            
            return $latlngs;
        } catch (\Exception $e) {
            Log::error('Error decoding polyline:', ['encodedString' => $encodedString, 'error' => $e->getMessage()]);
            return [];
        }
    }
}
