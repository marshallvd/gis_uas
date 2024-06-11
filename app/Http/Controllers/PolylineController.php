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
            if (isset($data_ruas_jalan['ruasjalan'])) {
                $ruasjalan = $data_ruas_jalan['ruasjalan'];
                $desa = $this->getDesa($token, $ruasjalan['desa_id']);
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

                return view('polyline.edit', compact('ruasjalan', 'provinces', 'kabupatens', 'kecamatans', 'desas', 'eksistings', 'kondisis', 'jenis_jalans', 'desa', 'kecamatan', 'kabupaten', 'provinsi'));
            } else {
                return redirect()->back()->with('error', 'Invalid ruasjalan data format.');
            }
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
            if (isset($data['desa'])) {
                return $data['desa']; // Return the desa data
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
            if (isset($data['kecamatan'])) {
                return $data['kecamatan']; // Return the kecamatan data
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
            if (isset($data['kabupaten'])) {
                return $data['kabupaten']; // Return the kabupaten data
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
            if (isset($data['provinsi'])) {
                foreach ($data['provinsi'] as $provinsi) {
                    if ($provinsi['id'] == $provinsiId) {
                        return $provinsi;
                    }
                }
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
            if (isset($data['provinsi'])) {
                return $data['provinsi'];
            }
        }

        return [];
    }

    private function fetchKabupatens($token, $provinsiId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/kabupaten', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            if (isset($data['kabupaten'])) {
                return array_filter($data['kabupaten'], function($kabupaten) use ($provinsiId) {
                    return $kabupaten['provinsi_id'] == $provinsiId;
                });
            }
        }

        return [];
    }

    private function fetchKecamatans($token, $kabupatenId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/kecamatan', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            if (isset($data['kecamatan'])) {
                return array_filter($data['kecamatan'], function($kecamatan) use ($kabupatenId) {
                    return $kecamatan['kabupaten_id'] == $kabupatenId;
                });
            }
        }

        return [];
    }

    private function fetchDesas($token, $kecamatanId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/desa', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            if (isset($data['desa'])) {
                return array_filter($data['desa'], function($desa) use ($kecamatanId) {
                    return $desa['kecamatan_id'] == $kecamatanId;
                });
            }
        }

        return [];
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
            $data = json_decode($response->getBody(), true);
            if (isset($data['eksisting'])) {
                return $data['eksisting'];
            }
        }

        return [];
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
            $data = json_decode($response->getBody(), true);
            if (isset($data['kondisi'])) {
                return $data['kondisi'];
            }
        }

        return [];
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
            $data = json_decode($response->getBody(), true);
            if (isset($data['jenisjalan'])) {
                return $data['jenisjalan'];
            }
        }

        return [];
    }

    public function update(Request $request, $id)
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
        $response = $client->request('PUT', 'https://gisapis.manpits.xyz/api/ruasjalan/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . session('token'),
                'Accept' => 'application/json',
            ],
            'json' => [
                'nama_ruas' => $request->name,
                'paths' => $encodedCoordinates,
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            return redirect()->route('polyline.index')->with('success', 'Polyline updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update polyline.');
        }
    }

    public function destroy($id)
    {
        $client = new Client();
        $response = $client->request('DELETE', 'https://gisapis.manpits.xyz/api/ruasjalan/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . session('token'),
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 204) {
            return redirect()->route('polyline.index')->with('success', 'Polyline deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete polyline.');
        }
    }

    private function encodePolyline($coordinates)
    {
        $encodedCoordinates = Polyline::encode(array_map(function ($coord) {
            return [$coord['lat'], $coord['lng']];
        }, $coordinates));

        return $encodedCoordinates;
    }
}
