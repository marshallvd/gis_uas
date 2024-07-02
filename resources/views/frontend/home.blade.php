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
        top: 150px; /* Sesuaikan dengan kebutuhan Anda */
        left: 40px; /* Posisikan sidebar di sebelah kiri */
        z-index: 1000;
        width: 210px; /* Sesuaikan lebar sidebar sesuai kebutuhan Anda */
        background-color: rgba(255, 255, 255, 0.8);
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
        padding: 10px;
        overflow-y: auto;
        height: calc(vh - 80px); /* Mengurangi tinggi sidebar agar tidak menutupi footer */
        flex-direction: column;
        border-radius: 8px; /* Efek rounded */
    }

    /* Memastikan konten peta berada di belakang sidebar */
    #map-container {
        position: relative;
        left: -500px
    }

    #map {
        height: calc(100vh - 80px); /* Sesuaikan tinggi peta agar tidak menutupi header */
        
        width: 2500px;
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
                right: -980px;
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

#tutorialModal {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999; /* Ensure it's above the map */
}

#tutorialModal .modal-content {
    background-color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    max-width: 100%;
    max-height: 80vh;
    overflow-y: auto;
}
.hidden {
    display: none !important;
}


    </style>
@endsection

@section('contents')
<div id="map-container">
    <div id="map"></div>
    
    <div class="absolute top-4 left-1/2 transform -translate-x-1/2 z-[1000] flex space-x-2">

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
        <select id="filter-jenis-jalan" class="select select-bordered w-full max-w-xs">
            <option value="">Jenis Jalan</option>
            <option value="1">Desa</option>
            <option value="2">Kabupaten</option>
            <option value="3">Provinsi</option>
        </select>
        <select id="filter-kondisi-jalan" class="select select-bordered w-full max-w-xs">
            <option value="">Kondisi Jalan</option>
            <option value="1">Baik</option>
            <option value="2">Sedang</option>
            <option value="3">Rusak</option>
        </select>
        
        <input type="text" id="search-input" placeholder="Nama Jalan" class="input input-bordered w-full max-w-xs" />
        <button id="search-button" class="btn btn-primary">Cari</button>
        <button id="reset-button" class="btn btn-secondary">Reset</button>

        

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

<div class="sidebar" id="sidebar">
    <div class="header">
        <span class="description-header"><h1>Menu</h1></span>
    </div>
    <div class="main flex flex-col h-80">
        <div class="list-item flex-grow">
            <a href="{{ route('home')}}" class="flex items-center space-x-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('polyline.create', ['previous' => 'home']) }}" class="flex items-center space-x-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Ruas Jalan</span>
            </a>
        
            <a href="{{ route('polyline.index') }}" class="flex items-center space-x-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Data Ruas Jalan</span>
            </a>

            <!-- Add Tutorial button -->
            <a  href="#tutorialModal" class="sidebar-link">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Tutorial</span>
            </a>

        </div>
        
        <div class="">
            <button onclick="confirmLogout(event)" class="btn btn-error w-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </div>
    </div>
</div>

<!-- Modal Tutorial -->
<div id="tutorialModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50  items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full">
        <div class="p-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Tutorial Penggunaan Jalanan</h3>
            <div class="mt-4 text-left">
                <ol class="list-decimal pl-5 space-y-3 text-gray-700">
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Memulai:</strong> Buka halaman home untuk melihat peta interaktif yang menampilkan ruas-ruas jalan.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Navigasi Peta:</strong> Gunakan mouse untuk menggeser peta dan roda mouse untuk zoom.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Mengubah Tampilan Peta:</strong> Klik ikon berlapis di pojok kanan atas untuk memilih jenis peta.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Menggunakan Sidebar:</strong> Klik tombol toggle sidebar di pojok kiri atas untuk membuka/menutup sidebar.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Mencari Ruas Jalan:</strong> Gunakan kotak pencarian di atas peta untuk mencari jalan.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Memfilter Ruas Jalan:</strong> Gunakan dropdown filter untuk menyaring jalan berdasarkan jenis atau kondisi.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Melihat Informasi Ruas Jalan:</strong> Klik pada garis jalan di peta untuk membuka popup informasi.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Menggunakan Tombol Aksi:</strong> Gunakan tombol Detail, Edit, atau Delete pada popup untuk mengelola data jalan.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Menambah Ruas Jalan Baru:</strong> Klik "Tambah Ruas Jalan" di sidebar.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Melihat Legenda:</strong> Lihat legenda di pojok kiri dan kanan bawah peta.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Reset Tampilan:</strong> Klik tombol "Reset" untuk menghapus semua filter dan pencarian.
                    </li>
                    <li class="hover:bg-gray-100 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Logout:</strong> Klik tombol "Logout" di bagian bawah sidebar untuk keluar.
                    </li>
                </ol>
            </div>
            <div class="mt-6">
                <button id="closeTutorial" class="btn btn-primary w-full">
                    Tutup Tutorial
                </button>
            </div>
        </div>
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    const closeTutorialButton = document.getElementById('closeTutorial');
    const tutorialLink = document.querySelector('a[href="#tutorialModal"]');
    const tutorialModal = document.getElementById('tutorialModal');

    // Pastikan tutorial tidak muncul otomatis
    if (tutorialModal) {
        tutorialModal.classList.add('hidden');
    }

    if (closeTutorialButton) {
        closeTutorialButton.addEventListener('click', function(event) {
            event.preventDefault();
            console.log('Close button clicked');
            closeTutorial();
        });
    } else {
        console.error('Close tutorial button not found');
    }

    if (tutorialLink) {
        tutorialLink.addEventListener('click', function(event) {
            event.preventDefault();
            console.log('Tutorial link clicked');
            showTutorial();
        });
    }

    // Tambahkan event listener untuk menutup modal saat mengklik di luar modal
    if (tutorialModal) {
        tutorialModal.addEventListener('click', function(event) {
            if (event.target === tutorialModal) {
                console.log('Modal background clicked');
                closeTutorial();
            }
        });
    }
});

function showTutorial() {
    console.log('Showing tutorial');
    const tutorialModal = document.getElementById('tutorialModal');
    if (tutorialModal) {
        tutorialModal.classList.remove('hidden');
        tutorialModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
}

function closeTutorial() {
    console.log('Closing tutorial');
    const tutorialModal = document.getElementById('tutorialModal');
    if (tutorialModal) {
        tutorialModal.classList.add('hidden');
        tutorialModal.classList.remove('flex');
        document.body.style.overflow = '';
    }
}
</script>

<script>
let polylineReferences = new Map();

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

        if (jenisJalan) {
            filteredData = filteredData.filter(road => road.jenisjalan_id.toString() === jenisJalan);
        }

        if (kondisiJalan) {
            filteredData = filteredData.filter(road => road.kondisi_id.toString() === kondisiJalan);
        }

        // Hapus polyline yang tidak ada lagi dalam data
        polylineReferences.forEach((polyline, id) => {
            if (!filteredData.some(road => road.id === id)) {
                map.removeLayer(polyline);
                polylineReferences.delete(id);
            }
        });

        drawPolylines(filteredData, filterType, searchValue !== '');
    })
    .catch(error => {
        console.error('Error fetching polyline data:', error);
    });
}

function deletePolyline(id) {
    const token = localStorage.getItem('token');
    const api_main_url = localStorage.getItem('api_main_url');
    
    if (!token || !api_main_url) {
        console.error('Token or API URL not found');
        return;
    }

    const url = `${api_main_url}ruasjalan/${id}`;
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Delete response:', data);
        
        if (data.status === 'failed') {
            throw new Error(data.message || 'Gagal menghapus ruas jalan');
        }

        // Hapus polyline dan elemen terkait dari peta
        if (polylineReferences.has(id)) {
            const polyline = polylineReferences.get(id);
            if (map && typeof map.removeLayer === 'function') {
                map.removeLayer(polyline);
                
                // Hapus marker dan label terkait
                map.eachLayer(layer => {
                    if (layer instanceof L.Marker || (layer instanceof L.Marker && layer.options.icon instanceof L.DivIcon)) {
                        map.removeLayer(layer);
                    }
                });
            }
            polylineReferences.delete(id);
        }

        Swal.fire(
            'Terhapus!',
            'Data telah berhasil dihapus.',
            'success'
        );

        // Refresh data setelah penghapusan
        fetchPolylineData('', '', '', '');
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire(
            'Error!',
            error.message || 'Terjadi kesalahan saat menghapus data.',
            'error'
        );
    });
}

// Pastikan window.handleDeleteButton juga didefinisikan di scope yang tepat
window.handleDeleteButton = function(id) {
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
            deletePolyline(id);
        }
    });
}

function clearMap() {
    map.eachLayer(layer => {
        if (layer instanceof L.Polyline || layer instanceof L.Marker) {
            map.removeLayer(layer);
        }
    });
}

    function generatePolylineDetailUrl(id) {
        return `${window.location.origin}/polyline/${id}`;
    }

let api_main_url;
let token;
    
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

    window.map = map;

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
        // clearMap();
        const searchInput = document.getElementById('search-input').value.trim().toLowerCase();
        let polylineFound = false;

        window.map.eachLayer(layer => {
            if (layer instanceof L.Polyline || layer instanceof L.Marker) {
                window.map.removeLayer(layer);
            }
        });

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
            polylineReferences.set(polyline.id, line);
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
                    
                    <p><strong>Eksisting:</strong> ${getNamaEksisting(polyline.eksisting_id)}</p>
                </div>
                <div class="popup-actions">
                    
                    <a href="${generatePolylineDetailUrl(polyline.id)}" class="btn btn-warning text-white">Detail</a>
            
                    <a href="/polyline/${polyline.id}/edit?previous=home" class="btn btn-accent text-white">Edit</a>
                    
                    <button onclick="handleDeleteButton(${polyline.id})" class="btn btn-error">Delete</button>
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

    // function confirmLogout(event) {
    //     event.preventDefault();
    //     Swal.fire({
    //         title: 'Apakah Anda yakin ingin keluar?',
    //         text: "Anda akan keluar dari sesi ini.",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Ya, keluar!',
    //         cancelButtonText: 'Batal'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             document.getElementById('logoutForm').submit();
    //         }
    //     });
    // }

    function confirmLogout(event) {
    event.preventDefault(); // Menghentikan aksi default tombol
    
    if (confirm('Are you sure you want to logout?')) {
        // Lakukan logout
        fetch('/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Jangan lupa sertakan token jika diperlukan
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            },
            body: JSON.stringify({}) // Data tambahan jika diperlukan
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Logout failed.');
            }
            // Bersihkan token dan arahkan ke halaman login
            localStorage.removeItem('token');
            window.location.href = '/login'; // Ganti sesuai dengan rute login Anda
        })
        .catch(error => {
            console.error('Logout error:', error);
            // Handle error
        });
    }
}

    // Inisialisasi peta dan data
    fetchPolylineData('', '', '', '');
});
</script>
@endpush