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

        .leaflet-popup-content-wrapper {
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .leaflet-popup-content {
            margin: 0;
            line-height: 1.5;
        }

        .custom-popup .leaflet-popup-content-wrapper {
            background-color: transparent;
            box-shadow: none;
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
            <thead class="bg-gray-700 text-white">
                <tr>
                    <th class="p-2">Nama Ruas</th>
                    <th class="p-2">Koordinat</th>
                    <th class="p-2">Panjang</th>
                    <th class="p-2">Lebar</th>
                    <th class="p-2">Eksisting</th>
                    <th class="p-2">Kondisi</th>
                    <th class="p-2">Jenis Jalan</th>
                    <th class="p-2">Keterangan</th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody id="polylineTableBody">
                @if(isset($polylines['ruasjalan']) && is_array($polylines['ruasjalan']))
                    @foreach ($polylines['ruasjalan'] as $polyline)
                    <tr>
                        <td class="p-2">{{ $polyline['nama_ruas'] }}</td>
                        <td class="p-2 truncate" title="{{ $polyline['paths'] }}">{{ Str::limit($polyline['paths'], 30) }}</td>
                        <td class="p-2">{{ $polyline['panjang'] }}</td>
                        <td class="p-2">{{ $polyline['lebar'] }}</td>
                        <td class="p-2">{{ $polyline['eksisting_id'] }}</td>
                        <td class="p-2">{{ $polyline['kondisi_id'] }}</td>
                        <td class="p-2">{{ $polyline['jenisjalan_id'] }}</td>
                        <td class="p-2">{{ $polyline['keterangan'] }}</td>
                        <td class="p-2 flex space-x-2">
                            <a href="{{ route('polyline.edit', $polyline['id']) }}" class="btn btn-accent">Edit</a>
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
        const api_main_url = localStorage.getItem("https://gisapis.manpits.xyz/api/");

        if (!token || !api_main_url) {
            console.error('Token or API URL is missing');
            return;
        }

        const headers = {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        };

        async function fetchRuasJalan() {
            const response = await axios.get(api_main_url + "ruasjalan", { headers });
            if (response.status !== 200) {
                throw new Error('Failed to fetch ruas jalan data: ' + response.statusText);
            }
            return response.data;
        }


        async function fetchData(url) {
            const response = await axios.get(url, { headers });
            if (response.status !== 200) {
                throw new Error('Failed to fetch data: ' + response.statusText);
            }
            return response.data;
        }

        try {
            console.log('Fetching data from API...');
            const data_ruas = await fetchData(api_main_url + "ruasjalan");
            const eksistingData = await fetchData(api_main_url + "meksisting");
            const kondisiData = await fetchData(api_main_url + "mkondisi");
            const jenisJalanData = await fetchData(api_main_url + "mjenisjalan");

            console.log('Data Ruas:', data_ruas);

            const tableBody = document.getElementById("polylineTableBody");
            const map = L.map('map').setView([-0.789275, 113.921327], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Tambahkan polylines ke peta
            const polylinesArray = [];
            if (Array.isArray(data_ruas.ruasjalan)) {
                data_ruas.ruasjalan.forEach(ruas => {
                    if (typeof ruas === 'object' && ruas !== null && 'nama_ruas' in ruas) {
                        console.log('Processing ruas:', ruas);

                        // Tambahkan polyline ke peta
                        const polyline = L.polyline(JSON.parse(ruas.paths), { color: 'blue' }).addTo(map);
                        map.fitBounds(polyline.getBounds());

                        // Tambahkan popup ke polyline
                        const eksisting = eksistingData.eksisting.find(e => e.id == ruas.eksisting_id);
                        const kondisi = kondisiData.kondisi.find(k => k.id == ruas.kondisi_id);
                        const jenisjalan = jenisJalanData.jenisjalan.find(j => j.id == ruas.jenisjalan_id);

                        const popupContent = `
                            <div class="p-4">
                                <h3 class="text-lg font-bold mb-2">${ruas.nama_ruas}</h3>
                                <p><span class="font-bold">Panjang:</span> ${ruas.panjang} meter</p>
                                <p><span class="font-bold">Lebar:</span> ${ruas.lebar} meter</p>
                                <p><span class="font-bold">Eksisting:</span> ${eksisting ? eksisting.nama : '-'}</p>
                                <p><span class="font-bold">Kondisi:</span> ${kondisi ? kondisi.nama : '-'}</p>
                                <p><span class="font-bold">Jenis Jalan:</span> ${jenisjalan ? jenisjalan.nama : '-'}</p>
                                <p><span class="font-bold">Keterangan:</span> ${ruas.keterangan}</p>
                            </div>
                        `;

                        const popup = L.popup({
                            maxWidth: 300,
                            className: 'custom-popup'
                        }).setContent(popupContent);
                        polyline.bindPopup(popup);

                        // Tambahkan polyline ke dalam array polylinesArray
                        polylinesArray.push(polyline);

                        // Tambahkan data ke tabel
                        const newRow = document.createElement("tr");
                        newRow.innerHTML = `
                            <td class="p-2">${ruas.nama_ruas}</td>
                            <td class="p-2 truncate" title="${ruas.paths}">${ruas.paths.slice(0, 30)}...</td>
                            <td class="p-2">${ruas.panjang}</td>
                            <td class="p-2">${ruas.lebar}</td>
                            <td class="p-2">${eksisting ? eksisting.nama : '-'}</td>
                            <td class="p-2">${kondisi ? kondisi.nama : '-'}</td>
                            <td class="p-2">${jenisjalan ? jenisjalan.nama : '-'}</td>
                            <td class="p-2">${ruas.keterangan}</td>
                        `;
                        tableBody.appendChild(newRow);
                    } else {
                        console.error('Invalid ruas data:', ruas);
                    }
                });
            }

            // Atur tampilan peta agar memperlihatkan semua polyline yang ditambahkan
            const group = new L.featureGroup(polylinesArray); // Mengumpulkan semua polyline dalam sebuah feature group
            map.fitBounds(group.getBounds()); // Mengatur tampilan peta agar menampilkan semua polyline dalam group

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


    var map = L.map('map').setView([-8.409518, 115.188919], 10);

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
