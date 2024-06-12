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
<div class="container mx-auto my-8">
    <div class="custom-card">
        <div class="custom-card-header">
            <h1 class="text-xl font-bold">Edit Data Jalan</h1>
            <a href="{{ route('polyline.index') }}" class="btn btn-outline btn-secondary">Kembali</a>
        </div>
        <div class="custom-card-body">
            <div id="map"></div>
        </div>
    </div>

    <div class="custom-card">
        <div class="custom-card-body">
            <form action="{{ route('polyline.update', $ruasJalan['id']) }}" method="POST" enctype="multipart/form-data" id="form" name="form">
                @csrf
                @method('PUT')
                <div class="form-section">
                    <!-- Bagian Pertama -->
                    <div>
                        {{-- @php
                            dd($data_ruas_jalan);
                        @endphp --}}
                        <!-- Nama Ruas -->
                        <div class="form-control">
                            <label class="label" for="nama_ruas">
                                <span class="label-text"><b>Nama Ruas</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="nama_ruas" name="nama_ruas" value="{{ $ruasJalan['nama_ruas'] }}" required />



                        </div>
                        <!-- Lebar Ruas -->
                        <div class="form-control">
                            <label class="label" for="lebar">
                                <span class="label-text"><b>Lebar Ruas</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="lebar" name="lebar" value="{{ $ruasJalan['lebar'] }}" required />

                        </div>
                        <!-- Kode Ruas -->
                        <div class="form-control">
                            <label class="label" for="kode_ruas">
                                <span class="label-text"><b>Kode Ruas</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="kode_ruas" name="kode_ruas" value="{{ $ruasJalan['kode_ruas'] }}" required />
                        </div>
                        <!-- Keterangan -->
                        <div class="form-control">
                            <label class="label" for="keterangan">
                                <span class="label-text"><b>Keterangan</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="keterangan" name="keterangan" value="{{ $ruasJalan['keterangan'] }}" required />
                        </div>
                    </div>

                    <!-- Bagian Kedua -->
                    <div>
                        {{-- @php
                            dd($provinces);
                        @endphp --}}
                        <!-- Dropdown Provinsi -->
                        <div class="form-control">
                            <label class="label" for="province">
                                <span class="label-text"><b>Pilih Provinsi</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="province" name="province" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach($regionData['provinsi'] as $provinsi)
                                    <option value="{{ $provinsi['id'] }}" {{ $ruasJalan['province_id'] == $provinsi['id'] ? 'selected' : '' }}>{{ $provinsi['provinsi'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        

                        <!-- Dropdown Kabupaten -->
                        {{-- @php
                            dd($kabupatens);
                        @endphp --}}
                        
                        <div class="form-control">
                            <label class="label" for="kabupaten">
                                <span class="label-text"><b>Pilih Kabupaten</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="kabupaten" name="kabupaten" required>
                                <option value="">Pilih Kabupaten</option>
                                @foreach($regionData['kabupaten'] as $kabupaten)
                                    <option value="{{ $kabupaten['id'] }}" {{ $ruasJalan['kabupaten_id'] == $kabupaten['id'] ? 'selected' : '' }}>{{ $kabupaten['kabupaten'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Dropdown Kecamatan -->
                        {{-- @php
                            dd($kecamatans);
                        @endphp --}}
                        
                        <div class="form-control">
                            <label class="label" for="kecamatan">
                                <span class="label-text"><b>Pilih Kecamatan</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="kecamatan" name="kecamatan" required>
                                <option value="">Pilih Kecamatan</option>
                                @foreach($regionData['kecamatan'] as $kecamatan)
                                    <option value="{{ $kecamatan['id'] }}" {{ $ruasJalan['kecamatan_id'] == $kecamatan['id'] ? 'selected' : '' }}>{{ $kecamatan['kecamatan'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Dropdown Desa -->
                        {{-- @php
                            dd($desas);
                        @endphp --}}
                        <div class="form-control">
                            <label class="label" for="desa">
                                <span class="label-text"><b>Pilih Desa</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="desa" name="desa" required>
                                <option value="">Pilih Desa</option>
                                @foreach($regionData['desa'] as $desa)
                                    <option value="{{ $desa['id'] }}" {{ $ruasJalan['desa_id'] == $desa['id'] ? 'selected' : '' }}>{{ $desa['desa'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Bagian Ketiga -->
                    <div>
                        {{-- @php
                            dd($eksistings);
                        @endphp --}}
                        <!-- Dropdown Eksisting -->
                        <div class="form-control">
                            <label class="label" for="eksisting">
                                <span class="label-text"><b>Eksisting</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="eksisting" name="eksisting" required>
                                <option value="">Pilih Material</option>
                                @foreach($eksistingData as $eksisting)
                                    <option value="{{ $eksisting['id'] }}" {{ $ruasJalan['eksisting_id'] == $eksisting['id'] ? 'selected' : '' }}>{{ $eksisting['eksisting'] }}</option>
                                @endforeach
                                {{-- @foreach($eksistings['eksisting'] as $eksisting)
                                    <option value="{{ $eksisting['id'] }}" {{ $ruasJalan['eksisting_id'] == $eksisting['id'] ? 'selected' : '' }}>{{ $eksisting['eksisting'] }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        <!-- Dropdown Kondisi -->
                        {{-- @php
                            dd($ruasJalan);
                        @endphp --}}
                        <div class="form-control">
                            <label class="label" for="kondisi">
                                <span class="label-text"><b>Kondisi</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="kondisi" name="kondisi" required>
                                <option value="">Pilih Kondisi</option>
                                @foreach($kondisiData as $kondisi)
                                    <option value="{{ $kondisi['id'] }}" {{ $ruasJalan['kondisi_id'] == $kondisi['id'] ? 'selected' : '' }}>{{ $kondisi['kondisi'] }}</option>
                                @endforeach
                                {{-- @foreach($kondisis['eksisting'] as $kondisi)
                                    <option value="{{ $kondisi['id'] }}" {{ $ruasJalan['kondisi_id'] == $kondisi['id'] ? 'selected' : '' }}>{{ $kondisi['kondisi'] }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        
                        <!-- Dropdown Jenis Jalan -->
                        {{-- @php
                            dd($jenis_jalans);
                        @endphp --}}
                        <!-- Dropdown Jenis Jalan -->
                        <div class="form-control">
                            <label class="label" for="jenis_jalan">
                                <span class="label-text"><b>Jenis Jalan</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="jenis_jalan" name="jenis_jalan" required>
                                <option value="">Pilih Jenis Jalan</option>
                                @foreach($jenisjalanData as $jenisjalan)
                                    <option value="{{ $jenisjalan['id'] }}" {{ $ruasJalan['jenisjalan_id'] == $jenisjalan['id'] ? 'selected' : '' }}>{{ $jenisjalan['jenisjalan'] }}</option>
                                @endforeach
                                {{-- @if(isset($jenis_jalans['eksisting']))
                                    @foreach($jenis_jalans['eksisting'] as $jenis_jalan)
                                        <option value="{{ $jenis_jalan['id'] }}" {{ isset($ruasJalan['jenisjalan_id']) && $ruasJalan['jenisjalan_id'] == $jenis_jalan['id'] ? 'selected' : '' }}>{{ $jenis_jalan['jenisjalan'] }}</option>
                                @endforeach
                                @endif --}}
                                
                            </select>
                        </div>

                        <!-- Latlng (Koordinat) -->
                        <div class="form-control">
                            <label class="label" for="latlng">
                                <span class="label-text"><b>Latlng</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="latlng" name="latlng" value="{{ $ruasJalan['paths'] }}" required />

                        </div>
                    </div>
                </div>

                <!-- Tombol Submit dan Reset -->
                <div class="flex justify-between">
                    <button type="submit" class="btn btn-primary w-1/2 mt-4">Update Jalan</button>
                    <button type="reset" class="btn btn-accent w-1/2 mt-4 ml-2">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
<meta name="api-token" content="{{ session('token') }}">
@endsection

@push('javascript')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-geometryutil@0.0.2/dist/leaflet.geometryutil.min.js"></script>

<script>
    var polylineData = {!! json_encode($ruasJalan['paths']) !!};
</script>

<script>
    // Initial map setup
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

    // FeatureGroup is to store editable layers
    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
        edit: {
            featureGroup: drawnItems
        },
        draw: {
            polyline: true,
            polygon: false,
            circle: false,
            rectangle: false,
            marker: false,
            circlemarker: false
        }
    });
    map.addControl(drawControl);

    function calculateLength(latlngs) {
        var totalLength = 0;

        for (var i = 1; i < latlngs.length; i++) {
            totalLength += latlngs[i - 1].distanceTo(latlngs[i]);
        }

        return totalLength;
    }

    function updateLatLngInput(layer) {
        var latlngs = layer.getLatLngs();
        var latlngString = latlngs.map(function(latlng) {
            return `${latlng.lat},${latlng.lng}`;
        }).join(' ');

        document.getElementById('latlng').value = latlngString;

        var length = calculateLength(latlngs);
        console.log('Length:', length);
        alert(`Panjang Polyline: ${length.toFixed(2)} meters`);
    }

    map.on(L.Draw.Event.CREATED, function (event) {
        var layer = event.layer;
        updateLatLngInput(layer);
        drawnItems.addLayer(layer);
    });

    map.on(L.Draw.Event.EDITED, function (event) {
        var layers = event.layers;
        layers.eachLayer(function (layer) {
            updateLatLngInput(layer);
        });
    });

    // Adding existing polyline data
    var polylineData = {!! json_encode($ruasJalan['paths']) !!};

    // Convert the string of coordinates into an array of LatLng objects
    var polylineLatLngs = polylineData.split(' ').map(function(coords) {
        var latlng = coords.split(',');
        return L.latLng(parseFloat(latlng[0]), parseFloat(latlng[1]));
    });

    // Create the polyline and add it to the map
    var existingPolyline = L.polyline(polylineLatLngs, {color: 'blue'}).addTo(map);
    drawnItems.addLayer(existingPolyline);

    document.getElementById('form').addEventListener('reset', function() {
        // Menghapus semua layer dari drawnItems ketika tombol reset ditekan
        drawnItems.clearLayers();
        // Reset koordinat
        document.getElementById('latlng').value = '';
    });



    document.getElementById('form').addEventListener('reset', function() {
        // Menghapus semua layer dari drawnItems ketika tombol reset ditekan
        drawnItems.clearLayers();
        // Reset koordinat
        document.getElementById('latlng').value = '';
    });


    document.addEventListener('DOMContentLoaded', function () {
        const token = document.querySelector('meta[name="api-token"]').getAttribute('content');
        const api_main_url = 'https://gisapis.manpits.xyz/api/';
        

        document.getElementById('form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = {
                paths: document.getElementById('latlng').value,
                desa_id: document.getElementById('desa').value,
                kode_ruas: document.getElementById('kode_ruas').value,
                nama_ruas: document.getElementById('nama_ruas').value,
                panjang: calculateLength(drawnItems.getLayers()[0].getLatLngs()),
                lebar: parseFloat(document.getElementById('lebar').value),
                eksisting_id: parseInt(document.getElementById('eksisting').value),
                kondisi_id: parseInt(document.getElementById('kondisi').value),
                jenisjalan_id: parseInt(document.getElementById('jenis_jalan').value),
                keterangan: document.getElementById('keterangan').value
            };

            const idRuasJalan = "{{ $ruasJalan['id'] }}";
            const token = document.querySelector('meta[name="api-token"]').getAttribute('content');

            axios.put(`https://gisapis.manpits.xyz/api/ruasjalan/${idRuasJalan}`, formData, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            })
            .then(response => {
                console.log('Data berhasil diperbarui:', response.data);
                alert('Data berhasil diperbarui.');
                window.location.href = "{{ route('polyline.index') }}";
            })
            .catch(error => {
                console.error('Terjadi kesalahan:', error);
                if (error.response) {
                    console.error('Server mengembalikan status error:', error.response.status);
                    console.error('Data error:', error.response.data);
                    alert(`Terjadi kesalahan: ${error.response.data.message}`);
                } else {
                    console.error('Kesalahan saat menyiapkan permintaan:', error.message);
                    alert(`Terjadi kesalahan: ${error.message}`);
                }
            });
        });
    });
</script>


@endpush
