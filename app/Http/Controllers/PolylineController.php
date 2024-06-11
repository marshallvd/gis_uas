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

    public function create()
    {
        return view('polyline.create');
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
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/ruasjalan/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $data_ruas_jalan = json_decode($response->getBody(), true);

            if (!isset($data_ruas_jalan['ruasjalan'])) {
                Log::warning('Invalid data format:', (array) $data_ruas_jalan);
                return redirect()->back()->with('error', 'Invalid data format from API');
            }

            $ruasJalan = $data_ruas_jalan['ruasjalan'];

            // Fetch additional data
            $desa = $this->getDesa($token, $ruasJalan['desa_id']);
            $kecamatan = $desa ? $this->getKecamatan($token, $desa['kecamatan_id']) : null;
            $kabupaten = $kecamatan ? $this->getKabupaten($token, $kecamatan['kabupaten_id']) : null;
            $provinsi = $kabupaten ? $this->getProvinsi($token, $kabupaten['provinsi_id']) : null;

            $provinces = $this->fetchProvinces($token);
            $kabupatens = $provinsi ? $this->fetchKabupatens($token, $provinsi['id']) : [];
            $kecamatans = $kabupaten ? $this->fetchKecamatans($token, $kabupaten['id']) : [];
            $desas = $kecamatan ? $this->fetchDesas($token, $kecamatan['id']) : [];
            $eksistings = $this->fetchEksistings($token);
            $kondisis = $this->fetchKondisis($token);
            $jenis_jalans = $this->fetchJenisJalans($token);

            return view('polyline.edit', compact('ruasJalan', 'provinces', 'kabupatens', 'kecamatans', 'desas', 'eksistings', 'kondisis', 'jenis_jalans', 'desa', 'kecamatan', 'kabupaten', 'provinsi'));
        } else {
            return redirect()->back()->with('error', 'Failed to fetch data from API');
        }
    }
        

    
    private function getDesa($token, $desaId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/desa/' . $desaId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    
        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            if (isset($data['desa']) && is_array($data['desa'])) {
                return $data['desa']; // Mengasumsikan hanya ada satu desa yang dikembalikan
            }
        }
    
        return null;
    }
    
    private function getKecamatan($token, $kecamatanId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/kecamatan/' . $kecamatanId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    
        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            if (isset($data['kecamatan']) && is_array($data['kecamatan'])) {
                return $data['kecamatan']; // Mengasumsikan hanya ada satu kecamatan yang dikembalikan
            }
        }
    
        return null;
    }
    
    private function getKabupaten($token, $kabupatenId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/kabupaten/' . $kabupatenId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    
        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            if (isset($data['kabupaten']) && is_array($data['kabupaten'])) {
                return $data['kabupaten']; // Mengasumsikan hanya ada satu kabupaten yang dikembalikan
            }
        }
    
        return null;
    }
    
    private function getProvinsi($token, $provinsiId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/mregion', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    
        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            if (isset($data['provinsi']) && is_array($data['provinsi'])) {
                $provinsi = array_filter($data['provinsi'], function ($p) use ($provinsiId) {
                    return $p['id'] == $provinsiId;
                });
                return array_values($provinsi) ?? null;
            }
        }
    
        return null;
    }


    private function fetchProvinces($token)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/mregion', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    
        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            if (isset($data['provinsi']) && is_array($data['provinsi'])) {
                return $data['provinsi'];
            } else {
                Log::warning('Invalid provinces data format:', (array)$data);
                return [];
            }
        } else {
            Log::warning('Failed to fetch provinces data:', ['status_code' => $response->getStatusCode()]);
            return [];
        }
    }

    private function fetchKabupatens($token, $provinsiId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/kabupaten/${provinceId}', [
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

    private function fetchKecamatans($token, $kabupatenId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/kecamatan/${kabupatenId}', [
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

    private function fetchDesas($token, $kecamatanId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/desa/${kecamatanId}' , [
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

    private function fetchEksistings($token)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/meksisting', [
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

    private function fetchKondisis($token)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/mkondisi', [
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

    private function fetchJenisJalans($token)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/mjenisjalan', [
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

    public function update(Request $request, $id)
    {
        $token = session('token');

        $client = new Client();
        $response = $client->request('PUT', 'https://gisapis.manpits.xyz/api/ruasjalan/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
            'json' => [
                'kodeRuas' => $request->kodeRuas,
                'namaRuas' => $request->namaRuas,
                'keterangan' => $request->keterangan,
                'lebar' => $request->lebar,
                'desaId' => $request->desaId,
                'jenisjalanId' => $request->jenisjalanId,
                'eksistingId' => $request->eksistingId,
                'kondisiId' => $request->kondisiId,
            ],
        ]);
    
        if ($response->getStatusCode() == 200) {
            return redirect()->route('polyline.index')->with('success', 'Data polyline berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui data polyline.');
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
