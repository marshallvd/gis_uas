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
    
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://gisapis.manpits.xyz/api/ruasjalan', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    
        if ($response->getStatusCode() == 200) {
            $polylines = json_decode($response->getBody(), true);
            // dd($polylines); // Tambahkan dd() untuk melihat struktur data
            return view('polyline.index', compact('polylines'));
        } else {
            // Handle error
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

    public function edit()
    {

        return view('polyline.edit',[
        
        ]);
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

