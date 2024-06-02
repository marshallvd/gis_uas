@extends('layouts.app')
@extends('layouts.header')

@section('css')
    <!-- Menambahkan stylesheet Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <style>
        #map { 
            height: 600px; 
            margin-top: 20px; 
        }

        .custom-card {
            background-color: #1a1a1a; /* Warna latar belakang card */
            border-radius: 10px; /* Mengatur sudut lengkung card */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5); /* Efek bayangan card */
            margin-bottom: 20px; /* Jarak antara card */
        }

        .custom-card-header {
            padding: 20px; /* Ruang dalam header card */
            border-bottom: 2px solid #333333; /* Garis bawah header card */
            border-radius: 10px 10px 0px 0px; /* Sudut lengkung hanya pada sudut atas */
            background-color: #1a1a1a; /* Warna header card */
            display: flex; /* Flexbox untuk align items */
            justify-content: space-between; /* Menyebar elemen secara horizontal */
            align-items: center; /* Align items secara vertikal */
        }

        .custom-card-body {
            padding: 20px; /* Ruang dalam body card */
        }

        .custom-label {
            font-weight: bold; /* Tulisan tebal pada label */
        }

        .form-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-section > div {
            flex: 1;
            margin: 0 10px;
        }
    </style>
@endsection

@section('contents')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-3">
        <h1 class="text-2xl font-bold">Data Polyline</h1>
        <a href="{{ route('polyline.create') }}" class="btn btn-outline btn-primary">Create Data</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <div id="map" class="w-full h-96 mb-4"></div>
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Nama Ruas</th>
                    <th>Koordinat</th>
                    <th>Panjang</th>
                    <th>Lebar</th>
                    <th>Eksisting</th>
                    <th>Kondisi</th>
                    <th>Jenis Jalan</th>
                    <th>Keterangan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="polylineTableBody">
                @if(isset($polylines['ruasjalan']) && is_array($polylines['ruasjalan']))
                    @foreach ($polylines['ruasjalan'] as $polyline)
                    <tr>
                        <td>{{ $polyline['nama_ruas'] }}</td>
                        <td>{{ $polyline['paths'] }}</td>
                        <td>{{ $polyline['panjang'] }}</td>
                        <td>{{ $polyline['lebar'] }}</td>
                        <td>{{ $polyline['eksisting_id'] }}</td>
                        <td>{{ $polyline['kondisi_id'] }}</td>
                        <td>{{ $polyline['jenisjalan_id'] }}</td>
                        <td>{{ $polyline['keterangan'] }}</td>
                        <td class="flex space-x-2">
                            <a href="{{ route('polyline.edit', $polyline['id']) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('polyline.destroy', $polyline['id']) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>                            
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection


@push('javascript')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-geometryutil@0.0.2/dist/leaflet.geometryutil.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', async function () {
        const token = localStorage.getItem("token");
        const api_main_url = localStorage.getItem("api_main_url");

        if (!token || !api_main_url) {
            console.error('Token or API URL is missing');
            return;
        }

        const headers = {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        };

        async function fetchData(url) {
            const response = await fetch(url, { headers });
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        }

        try {
            console.log('Fetching data from API...');
            const data_region = await fetchData(api_main_url + "api/mregion");
            const data_ruas = await fetchData(api_main_url + "api/ruasjalan");
            const eksistingData = await fetchData(api_main_url + "api/meksisting");
            const kondisiData = await fetchData(api_main_url + "api/mkondisi");
            const jenisJalanData = await fetchData(api_main_url + "api/mjenisjalan");

            console.log('Data Region:', data_region);
            console.log('Data Ruas:', data_ruas);

            const tableBody = document.getElementById("polylineTableBody");
            const map = L.map('map').setView([-0.789275, 113.921327], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            if (Array.isArray(data_ruas.ruasjalan)) {
                data_ruas.ruasjalan.forEach(ruas => {
                    if (typeof ruas === 'object' && ruas !== null && 'nama_ruas' in ruas) {
                        console.log('Processing ruas:', ruas);

                        // Tambahkan polyline ke peta
                        const polyline = L.polyline(JSON.parse(ruas.paths), { color: 'blue' }).addTo(map);
                        map.fitBounds(polyline.getBounds());

                        // Tambahkan data ke tabel
                        const eksisting = eksistingData.eksisting.find(e => e.id == ruas.eksisting_id);
                        const kondisi = kondisiData.kondisi.find(k => k.id == ruas.kondisi_id);
                        const jenisjalan = jenisJalanData.jenisjalan.find(j => j.id == ruas.jenisjalan_id);

                        const newRow = document.createElement("tr");
                        newRow.innerHTML = `
                            <td>${ruas.nama_ruas}</td>
                            <td>${ruas.paths}</td>
                            <td>${ruas.panjang}</td>
                            <td>${ruas.lebar}</td>
                            <td>${eksisting ? eksisting.eksisting : '-'}</td>
                            <td>${kondisi ? kondisi.kondisi : '-'}</td>
                            <td>${jenisjalan ? jenisjalan.jenisjalan : '-'}</td>
                            <td>${ruas.keterangan}</td>
                            <td class="flex space-x-2">
                                <a href="/polyline/edit/${ruas.id}" class="btn btn-primary">Edit</a>
                                <form action="/polyline/destroy/${ruas.id}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        `;
                        tableBody.appendChild(newRow);
                    } else {
                        console.error('Invalid ruas data:', ruas);
                    }
                });
            } else {
                console.error('Invalid data_ruas.ruasjalan:', data_ruas.ruasjalan);
            }

            document.querySelectorAll('.btn-danger').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var form = this.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                            Swal.fire(
                                'Deleted!',
                                'Your data has been deleted.',
                                'success'
                            );
                        }
                    });
                });
            });

        } catch (error) {
            console.error('Error fetching data:', error);
        }
    });

    var map = L.map('map').setView([-8.409518, 115.188919], 13);

    // Adding multiple basemaps
    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var Esri_World = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    });

    var Esri_Map = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; National Geographic, Esri, DeLorme, NAVTEQ, UNEP-WCMC, USGS, NASA, ESA, METI, NRCAN, GEBCO, NOAA, iPC',
        maxZoom: 16
    });

    var Stadia_Dark = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.{ext}', {
        minZoom: 0,
        maxZoom: 20,
        attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        ext: 'png'
    });

    var baseLayers = {
        "OSM Tiles": tiles,
        "ESRI World Imagery": Esri_World,
        "ESRI Map": Esri_Map,
        "Stadia Dark": Stadia_Dark
    };

    // Adding layer control to map
    L.control.layers(baseLayers).addTo(map);

    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
        edit: {
            featureGroup: drawnItems
        },
        draw: {
            polyline: true,
            polygon: true,
            circle: false,
            rectangle: false,
            marker: false,
            circlemarker: false
        }
    });
    map.addControl(drawControl);

    map.on('draw:created', function (event) {
        var layer = event.layer;
        drawnItems.addLayer(layer);

        var latlngs;
        if (layer instanceof L.Polyline) {
            latlngs = layer.getLatLngs();
        } else if (layer instanceof L.Polygon) {
            latlngs = layer.getLatLngs()[0]; // outer ring
        }

        var latlngString = latlngs.map(function(latlng) {
            return `${latlng.lat}, ${latlng.lng}`;
        }).join('\n');

        document.getElementById('latlng').value = latlngString;

        // Calculate the length of the polyline
        var length = calculateLength(latlngs);
        console.log('Length:', length);

        // Display the length in a suitable HTML element (e.g., an input field or a div)
        alert(`Panjang Polyline: ${length.toFixed(2)} meters`);
    });

    map.on('draw:edited', function (event) {
        var layers = event.layers;
        var latlngs = [];

        layers.eachLayer(function (layer) {
            if (layer instanceof L.Polyline) {
                latlngs = latlngs.concat(layer.getLatLngs());
            } else if (layer instanceof L.Polygon) {
                latlngs = latlngs.concat(layer.getLatLngs()[0]); // outer ring
            }
        });

        var latlngString = latlngs.map(function(latlng) {
            return `${latlng.lat}, ${latlng.lng}`;
        }).join('\n');

        document.getElementById('latlng').value = latlngString;

        // Calculate the length of the polyline
        var length = calculateLength(latlngs);
        console.log('Length:', length);

        // Display the length in a suitable HTML element (e.g., an input field or a div)
        alert(`Panjang Polyline: ${length.toFixed(2)} meters`);
    });

    document.getElementById('form').addEventListener('reset', function() {
        // Menghapus semua layer dari drawnItems ketika tombol reset ditekan
        drawnItems.clearLayers();
        // Reset koordinat
        document.getElementById('latlng').value = '';
    });




</script>
@endpush
