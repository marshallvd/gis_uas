<?php
// app/Http/Controllers/PolylineController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Polyline;

class PolylineController extends Controller
{
    public function index()
    {
        $polylines = Polyline::all();
        return view('polyline.index', compact('polylines'));
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
}

