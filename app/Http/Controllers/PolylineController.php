<?php
// app/Http/Controllers/PolylineController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Polyline;
use GuzzleHttp\Client;

class PolylineController extends Controller
{
    public function index()
    {
        $token = session('token');
    
        $client = new Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/ruasjalan', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    
        if ($response->getStatusCode() == 200) {
            $polylines = json_decode($response->getBody(), true);
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

        $polyline = new Polyline();
        $polyline->name = $request->name;
        $polyline->coordinates = json_encode($request->coordinates);
        $polyline->save();

        return redirect()->route('polyline.index')->with('success', 'Polyline created successfully.');
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
            $data_region = $this->getDataRegion($token);
            return view('polyline.edit', compact('data_ruas_jalan', 'data_region'));
        } else {
            return redirect()->back()->with('error', 'Failed to fetch data from API');
        }
    }

    public function update(Request $request, $id)
    {
        $token = session('token');

        // Adaptasi dari JavaScript untuk mendapatkan data dari form
        $kodeRuas = $request->kodeRuas;
        $namaRuas = $request->namaRuas;
        $keterangan = $request->keterangan;
        $lebar = $request->lebar;
        $desaId = $request->desaId;
        $jenisjalanId = $request->jenisjalanId;
        $eksistingId = $request->eksistingId;
        $kondisiId = $request->kondisiId;

        // Lakukan validasi atau filter lainnya sesuai kebutuhan Anda

        // Panggil API untuk melakukan update data
        $client = new Client();
        $response = $client->request('PUT', 'https://gisapis.manpits.xyz/api/ruasjalan/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
            'json' => [ // Gunakan 'json' untuk mengirim data dalam format JSON
                'kodeRuas' => $kodeRuas,
                'namaRuas' => $namaRuas,
                'keterangan' => $keterangan,
                'lebar' => $lebar,
                'desaId' => $desaId,
                'jenisjalanId' => $jenisjalanId,
                'eksistingId' => $eksistingId,
                'kondisiId' => $kondisiId,
                // Tambahkan parameter lain sesuai kebutuhan
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            return redirect()->route('polyline.index')->with('success', 'Data polyline berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui data polyline.');
        }
    }

    // Fungsi untuk mendapatkan data region dari API
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
}

