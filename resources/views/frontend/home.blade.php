@extends('layouts.app')
@extends('layouts.header')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.1.6/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>

    .sidebar {
        position: absolute;
        top: 100px; /* Sesuaikan dengan kebutuhan Anda */
        left: 20px; /* Posisikan sidebar di sebelah kiri */
        z-index: 1000;
        width: 230px; /* Sesuaikan lebar sidebar sesuai kebutuhan Anda */
        background-color: rgba(255, 255, 255, 0.8);
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
        padding: 24px;
        overflow-y: auto;
        height: calc(45vh - 180px); /* Mengurangi tinggi sidebar agar tidak menutupi footer */
        flex-direction: column;
        border-radius: 8px; /* Efek rounded */
    }

    /* Memastikan konten peta berada di belakang sidebar */
    #map-container {
        position: relative;
    }

    #map {
        height: calc(100vh - 80px); /* Sesuaikan tinggi peta agar tidak menutupi header */
        width: 100%;
        overflow: hidden;
    }

        /* Mengatur transformasi ketika sidebar dalam keadaan terbuka */
        .sidebar.open {
            transform: translateX(0);
        }


        .sidebar .main .list-item a {
            display: flex;
            align-items: center; /* Center items vertically */
            padding: 10px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            color: #cbd5e0; /* Warna teks */
            font-size: 18px; /* Ukuran font */
        }

        .sidebar .main .list-item a:hover {
            background-color: #4a5568;
        }

        .sidebar .main .list-item a svg {
            flex-shrink: 0; /* Prevent SVG from shrinking */
        }

        .sidebar .main .list-item a span {
            margin-left: 4px; /* Jarak antara gambar dan teks */
        }


        .sidebar .header {
            margin-bottom: 1px; 
            text-align: center;/* Memberikan jarak antara header dan list item */
        }

        .sidebar .list-item {
            display: flex;
            flex-direction: column;
            gap: 1px; /* Memberikan jarak antara setiap item */
        }
        .sidebar .main.list-item .description{
            font-family: 'arial';
            font-style: normal;
            font-weight: 400;
            font-size: 12px;
            text-align: center;
            

        }

        .sidebar .main .list-item a {
            display: flex;
            align-items: center;
            padding: 12px 16px; /* Sesuaikan padding sesuai kebutuhan */
            transition: background-color 0.3s ease;
            text-decoration: none;
            color: #cbd5e0; /* Warna teks */
            font-size: 18px; /* Ukuran font */
        }

        .sidebar .main .list-item a:hover {
            background-color: #4a5568;
        }

        .sidebar .main .list-item a {
            font-family: 'Arial', sans-serif;
            font-size: 12px; /* Sesuaikan ukuran font sesuai keinginan Anda */
            color: #202228; /* Warna teks */
            text-decoration: none;
        }

        .sidebar .main .list-item a:hover {
            color: #202228; /* Warna teks saat di-hover */
            border-radius: 8px;
        }

        .sidebar .header h1 {
            font-family: 'Arial', sans-serif;
            font-size: 20px; /* Ukuran font header */
            font-weight: bold;
            color: #202228;
        }
        .sidebar .main .list-item #logoutForm button {
            display: block;
            width: 100%;
            padding: 10px;
            text-align: center;
            margin-top: 10px; /* Jarak atas tombol logout */
        }



    .legend-container,
            .legend-jenis {
                position: absolute;
                bottom: 20px;
                z-index: 1000;
                background-color: rgba(255, 255, 255, 0.8);
                padding: 10px;
                border-radius: 5px;
                width: 200px;
            }
        
            .legend-jenis {
                right: 20px;
            }
            .legend-container {
                left: 20px;
            }
        
            .legend-color {
                width: 20px;
                height: 20px;
                border-radius: 50%;
                display: inline-block;
                margin-right: 8px;
            }
        
            .legend-green {
                background-color: green;
            }
        
            .legend-orange {
                background-color: orange;
            }
        
            .legend-red {
                background-color: red;
            }

        .leaflet-popup-content-wrapper {
            font-family: 'Arial', sans-serif;
            border-radius: 8px;
            padding: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .leaflet-popup-content {
            font-size: 14px;
            width: 300px;
        }

        .leaflet-popup-content h4 {
            margin-bottom: 8px;
            font-size: 16px;
            font-weight: bold;
        }

        .leaflet-popup-content p {
            margin-bottom: 4px;
        }

        .popup-actions {
            display: flex;
            justify-content: space-evenly; /* Menyediakan ruang di antara tombol */
            margin-top: 12px; /* Atur jarak dari konten popup */
        }

        .popup-actions .btn {
            padding: 6px 12px; /* Menyesuaikan padding tombol */
        }

        .absolute .input,
        .absolute .select {
            background-color: rgba(255, 255, 255, 0.8);/* Warna latar belakang putih */
            color: #202228; /* Warna teks hitam */
            border-color: rgba(255, 255, 255, 0.8); /* Warna border */
        }

        .input:hover,
        .select:hover {
            border-color: #4a5568; /* Warna border saat hover */
        }

        .input:focus,
        .select:focus {
            border-color: #4caf50; /* Warna border saat fokus */
            box-shadow: 0 0 0 1px #4caf50; /* Efek bayangan saat fokus */
        }

        .custom-div-icon {
        background: none;
        border: none;
    }
    .custom-div-icon i {
        display: block;
        margin-top: -42px;
        margin-left: 5px;
        color: #fff;
        font-size: 18px;
    }
    .marker-pin {
        width: 30px;
        height: 30px;
        border-radius: 50% 50% 50% 0;
        position: absolute;
        transform: rotate(-45deg);
        left: 50%;
        top: 50%;
        margin: -15px 0 0 -15px;
    }
    .marker-pin::after {
        content: '';
        width: 24px;
        height: 24px;
        margin: 3px 0 0 3px;
        background: #fff;
        position: absolute;
        border-radius: 50%;
    }

    .polyline-label {
    background-color: rgba(255, 255, 255, 0.8);
    border: 1px solid #666;
    border-radius: 4px;
    padding: 2px 5px;
    font-weight: bold;
    font-size: 12px;
    white-space: nowrap;
    text-shadow: 1px 1px 1px #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

.a{
    color: #fff
}

    </style>
@endsection

@section('contents')
<div id="map-container">
    <div id="map"></div>
    <div class="sidebar" id="sidebar">
        <div class="header">
            <span class="description-header"><h1>Menu</h1></span>
        </div>
        <div class="main">
            <div class="list-item">
                <a href="{{ route('home')}}" class="flex items-center space-x-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('polyline.create', ['previous' => 'home']) }}" class="flex items-center space-x-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Tambah Ruas Jalan</span>
                </a>
            
                <a href="{{ route('polyline.index') }}" class="flex items-center space-x-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Data Ruas Jalan</span>
                </a>

                <button onclick="confirmLogout(event)" class="btn btn-error w-full">Logout</button>


            
            </div>
            
        </div>
    </div>
    <div class="absolute top-4 left-20 z-[1000] flex space-x-2">
        <label for="my-drawer-2" class="btn btn-accent" id="toggle-sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </label>
        <select id="filter" class="select select-bordered w-full max-w-xs">
            <option disabled selected>Pilih filter</option>
            <option value="">Semua</option>
            <option value="jenis">Jenis Jalan</option>
            <option value="kondisi">Kondisi Jalan</option>
        </select>
        <input type="text" id="search-input" placeholder="Nama Jalan" class="input input-bordered w-full max-w-xs" />
        <button id="search-button" class="btn btn-primary">Cari</button>
        <button id="reset-button" class="btn btn-secondary">Reset</button>

        <select id="filter-jenis-jalan" class="select select-bordered w-full max-w-xs">
            <option value="">Semua Jenis Jalan</option>
            <option value="1">Desa</option>
            <option value="2">Kabupaten</option>
            <option value="3">Provinsi</option>
        </select>
        <select id="filter-kondisi-jalan" class="select select-bordered w-full max-w-xs">
            <option value="">Semua Kondisi Jalan</option>
            <option value="1">Baik</option>
            <option value="2">Sedang</option>
            <option value="3">Rusak</option>
        </select>

    </div>

    <div class="legend-container">
        <h2 class="card-title">Kondisi Jalan</h2>
        <!-- Legend container -->
        <div class="legend-color legend-green"></div>
        <span>Baik</span>
        <br>
        <div class="legend-color legend-orange"></div>
        <span>Sedang</span>
        <br>
        <div class="legend-color legend-red"></div>
        <span>Rusak</span>
    </div>
    <div class="legend-jenis">
        <h2 class="card-title">Jenis Jalan</h2>
        <!-- Legend jenis jalan -->
        <div class="legend-color legend-green"></div>
        <span>Desa</span>
        <br>
        <div class="legend-color legend-orange"></div>
        <span>Kabupaten</span>
        <br>
        <div class="legend-color legend-red"></div>
        <span>Provinsi</span>
    </div>
</div>
@endsection

@push('javascript')
<script>
    localStorage.setItem("token", "{{ session('token') }}");
    localStorage.setItem("api_main_url", "https://gisapis.manpits.xyz/api/");
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-geometryutil@0.0.2/dist/leaflet.geometryutil.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

function handleDeleteButton(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${api_main_url}ruasjalan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire(
                        'Terhapus!',
                        'Data ruas jalan telah dihapus.',
                        'success'
                    );
                    fetchPolylineData(''); // Refresh data setelah penghapusan
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menghapus data.',
                        'error'
                    );
                });
            }
        });
    }

    function generatePolylineDetailUrl(id) {
        return `${window.location.origin}/polyline/${id}`;
    }
    
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    if (!token) {
        console.error('Token not found in localStorage');
        return;
    }

    const api_main_url = 'https://gisapis.manpits.xyz/api/';
    let desaData = [];
    let eksistingData = [];
    let kecamatanData = [];
    let kabupatenData = [];
    let regionData = [];
    let currentMarkers = [];

    var map = L.map('map').setView([-8.65, 115.22], 9.5);

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

    L.control.layers(baseLayers).addTo(map);

    function getNamaLokasi(desa_id) {
        const desa = desaData.find(d => d.id === desa_id);
        if (!desa) return "Tidak diketahui";
        
        const kecamatan = kecamatanData.find(k => k.id === desa.kecamatan_id);
        const kabupaten = kabupatenData.find(k => k.id === kecamatan?.kabupaten_id);
        const region = regionData.find(r => r.id === kabupaten?.region_id);

        return `${desa.desa}, ${kecamatan?.kecamatan || ''}, ${kabupaten?.kabupaten || ''}, ${region?.region || ''}`;
    }

    function fetchDesaData() {
        return fetch(`${api_main_url}mdesa`, {
            headers: {
                Authorization: `Bearer ${token}`,
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            console.log('Desa data received:', data);
            desaData = data.desa;
            return desaData;
        })
        .catch(error => {
            console.error('Error fetching desa:', error);
        });
    }

    function fetchEksistingData() {
        return fetch(`${api_main_url}meksisting`, {
            headers: {
                Authorization: `Bearer ${token}`,
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            console.log('Eksisting:', data.eksisting);
            eksistingData = data.eksisting;
        })
        .catch(error => {
            console.error('Error fetching eksisting:', error);
        });
    }

    function fetchKecamatanData() {
        return fetch(`${api_main_url}mkecamatan`, {
            headers: {
                Authorization: `Bearer ${token}`,
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            console.log('Kecamatan:', data.kecamatan);
            kecamatanData = data.kecamatan;
        })
        .catch(error => {
            console.error('Error fetching kecamatan:', error);
        });
    }

    function fetchKabupatenData() {
        return fetch(`${api_main_url}mkabupaten`, {
            headers: {
                Authorization: `Bearer ${token}`,
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            console.log('Kabupaten:', data.kabupaten);
            kabupatenData = data.kabupaten;
        })
        .catch(error => {
            console.error('Error fetching kabupaten:', error);
        });
    }

    function fetchRegionData() {
        return fetch(`${api_main_url}mregion`, {
            headers: {
                Authorization: `Bearer ${token}`,
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            console.log('Region:', data.region);
            regionData = data.region;
        })
        .catch(error => {
            console.error('Error fetching region:', error);
        });
    }

    function getNamaLokasi(desa_id) {
        console.log('Searching for desa_id:', desa_id);
        console.log('Available desa data:', desaData);

        const desa = desaData.find(d => {
            console.log('Comparing with:', d.id, typeof d.id);
            return d.id === parseInt(desa_id, 10);
        });

        if (!desa) {
            console.log('Desa not found');
            return "Tidak diketahui";
        }
        
        console.log('Found desa:', desa);

        const kecamatan = kecamatanData.find(k => k.id === desa.kecamatan_id);
        console.log('Found kecamatan:', kecamatan);

        const kabupaten = kabupatenData.find(k => k.id === kecamatan?.kabupaten_id);
        console.log('Found kabupaten:', kabupaten);

        const region = regionData.find(r => r.id === kabupaten?.region_id);
        console.log('Found region:', region);

        return `${desa.desa}, ${kecamatan?.kecamatan || ''}, ${kabupaten?.kabupaten || ''}, ${region?.region || ''}`;
    }

    function getNamaEksisting(id) {
        const eksisting = eksistingData.find(e => e.id === id);
        return eksisting ? eksisting.eksisting : "Tidak diketahui";
    }

    Promise.all([
        fetchDesaData(), 
        fetchEksistingData(), 
        fetchKecamatanData(), 
        fetchKabupatenData(), 
        fetchRegionData()
    ])
    .then(([desaData, eksistingData, kecamatanData, kabupatenData, regionData]) => {
        console.log('All data loaded');
        // Panggil fungsi untuk menampilkan polyline di sini
        fetchPolylineData('');
    })
    .catch(error => {
        console.error('Error initializing data:', error);
    });

    function drawPolylines(polylineData, filterType, isSearching) {
        const searchInput = document.getElementById('search-input').value.trim().toLowerCase();
        let polylineFound = false;

        const startIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:#4CAF50;' class='marker-pin'></div><i class='material-icons'>play_arrow</i>",
            iconSize: [30, 42],
            iconAnchor: [15, 42]
        });

        const endIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:#F44336;' class='marker-pin'></div><i class='material-icons'>stop</i>",
            iconSize: [30, 42],
            iconAnchor: [15, 42]
        });

        map.eachLayer(layer => {
            if (layer instanceof L.Polyline || layer instanceof L.Marker) {
                map.removeLayer(layer);
            }
        });

        function removeCurrentMarkers() {
            currentMarkers.forEach(marker => map.removeLayer(marker));
            currentMarkers = [];
        }

        polylineData.forEach(polyline => {
            const coordinates = polyline.paths.split(' ').map(coord => {
                const [lat, lng] = coord.trim().split(',').map(parseFloat);
                return [lat, lng];
            });

            if (!coordinates.every(coord => !isNaN(coord[0]) && !isNaN(coord[1]))) {
                console.error('Invalid coordinates:', coordinates);
                return;
            }

            let color = 'blue';
            if (filterType === 'jenis') {
                color = polyline.jenisjalan_id == 1 ? 'green' : polyline.jenisjalan_id == 2 ? 'orange' : 'red';
            } else if (filterType === 'kondisi') {
                color = polyline.kondisi_id == 1 ? 'green' : polyline.kondisi_id == 2 ? 'orange' : 'red';
            }

            const line = L.polyline(coordinates, { color }).addTo(map);

            const midpoint = coordinates[Math.floor(coordinates.length / 2)];
            const label = L.divIcon({
                className: 'polyline-label',
                html: polyline.kode_ruas,
                iconSize: [100, 20],
                iconAnchor: [50, -10]
            });
            L.marker(midpoint, { 
                icon: label, 
                interactive: false,
                zIndexOffset: 1000
            }).addTo(map);

            const popupContent = `
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-4">
                <h4>${polyline.nama_ruas}</h4>
                <div class="leaflet-popup-content">
                    <p><strong>ID:</strong> ${polyline.id}</p>
                    <p><strong>Kode Ruas:</strong> ${polyline.kode_ruas}</p>
                    <p><strong>Panjang:</strong> ${(polyline.panjang / 1000).toFixed(2)} km</p>
                    <p><strong>Lebar:</strong> ${polyline.lebar} m</p>
                    <p><strong>Kondisi:</strong> ${polyline.kondisi_id === 1 ? 'Baik' : polyline.kondisi_id === 2 ? 'Sedang' : 'Rusak'}</p>
                    <p><strong>Jenis Jalan:</strong> ${polyline.jenisjalan_id === 1 ? 'Desa' : polyline.jenisjalan_id === 2 ? 'Kabupaten' : 'Provinsi'}</p>
                    <p><strong>Lokasi:</strong> ${getNamaLokasi(polyline.desa_id)}</p>
                    <p><strong>Eksisting:</strong> ${getNamaEksisting(polyline.eksisting_id)}</p>
                </div>
                <div class="popup-actions">
                    
                    <a href="${generatePolylineDetailUrl(polyline.id)}" class="btn btn-warning text-white">Detail</a>
            
                    <a href="/polyline/${polyline.id}/edit?previous=home" class="btn btn-accent text-white">Edit</a>
                    
                    <button onclick="window.handleDeleteButton(${polyline.id})" class="btn btn-error">Delete</button>
                </div>
            </div>
        </div>
        `;

            line.on('click', function(e) {
                removeCurrentMarkers();

                const startMarker = L.marker(coordinates[0], {icon: startIcon}).addTo(map);
                const endMarker = L.marker(coordinates[coordinates.length - 1], {icon: endIcon}).addTo(map);
                
                currentMarkers.push(startMarker, endMarker);

                this.bindPopup(popupContent).openPopup(e.latlng);
            });

            if (isSearching && polyline.nama_ruas.toLowerCase().includes(searchInput)) {
                polylineFound = true;
                map.fitBounds(line.getBounds());
            }
        });

        if (isSearching && searchInput !== '' && !polylineFound) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Tidak ada jalan yang sesuai dengan pencarian Anda!',
                confirmButtonColor: '#4caf50',
            });
        }

        map.on('click', function(e) {
            if (!(e.originalEvent.target instanceof SVGElement)) {
                removeCurrentMarkers();
            }
        });
    }

    function fetchPolylineData(filterType, searchValue = '', jenisJalan = '', kondisiJalan = '') {
        console.log('Fetching data with:', { filterType, searchValue, jenisJalan, kondisiJalan });

        fetch(`${api_main_url}ruasjalan`, {
            headers: {
                'Authorization': `Bearer ${token}`,
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Received data:', data.ruasjalan);

            let filteredData = data.ruasjalan.filter(road => 
                road.nama_ruas.toLowerCase().includes(searchValue.toLowerCase())
            );

            console.log('After nama_ruas filter:', filteredData);

            if (jenisJalan) {
                filteredData = filteredData.filter(road => road.jenisjalan_id.toString() === jenisJalan);
                console.log('After jenis jalan filter:', filteredData);
            }

            if (kondisiJalan) {
                filteredData = filteredData.filter(road => road.kondisi_id.toString() === kondisiJalan);
                console.log('After kondisi jalan filter:', filteredData);
            }

            drawPolylines(filteredData, filterType, searchValue !== '');
        })
        .catch(error => {
            console.error('Error fetching polyline data:', error);
        });
    }

    const filterSelect = document.getElementById('filter');
    const searchInput = document.getElementById('search-input');
    const filterJenisJalan = document.getElementById('filter-jenis-jalan');
    const filterKondisiJalan = document.getElementById('filter-kondisi-jalan');

    filterJenisJalan.addEventListener('change', () => {
        fetchPolylineData(filterSelect.value, searchInput.value, filterJenisJalan.value, filterKondisiJalan.value);
    });

    filterKondisiJalan.addEventListener('change', () => {
        fetchPolylineData(filterSelect.value, searchInput.value, filterJenisJalan.value, filterKondisiJalan.value);
    });

    filterSelect.addEventListener('change', () => {
        fetchPolylineData(filterSelect.value, searchInput.value, filterJenisJalan.value, filterKondisiJalan.value);
    });

    const searchButton = document.getElementById('search-button');
    searchButton.addEventListener('click', () => {
        fetchPolylineData(filterSelect.value, searchInput.value, filterJenisJalan.value, filterKondisiJalan.value);
    });

    const resetButton = document.getElementById('reset-button');
    resetButton.addEventListener('click', () => {
        searchInput.value = '';
        filterSelect.value = '';
        filterJenisJalan.value = '';
        filterKondisiJalan.value = '';
        fetchPolylineData('', '', '', '');
    });

    const toggleSidebarButton = document.getElementById('toggle-sidebar');
    const sidebar = document.getElementById('sidebar');
    toggleSidebarButton.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin ingin keluar?',
            text: "Anda akan keluar dari sesi ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logoutForm').submit();
            }
        });
    }

    // Inisialisasi peta dan data
    fetchPolylineData('', '', '', '');
});
</script>
@endpush