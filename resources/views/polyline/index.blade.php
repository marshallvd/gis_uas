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

    #popupInfo {
        z-index: 9999;
    }

    #popupInfo .bg-opacity-50 {
        backdrop-filter: blur(10px);
    }

    #popupInfo .bg-white {
        max-width: 600px;
    }

    #popupInfo #popupContent p {
        margin-bottom: 0.5rem;
    }


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
        <h1 class="text-3xl font-bold">Data Polyline</h1>
        <a href="{{ route('polyline.create') }}" class="btn btn-outline btn-primary">Create Data</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <div id="map" class="w-full h-96 mb-4"></div>
        <table class="table w-full table-auto">
            <thead class="bg-gray-700 text-white">
                <tr>
                    <th class="p-2 text-center">Nama Ruas</th>
                    <th class="p-2 text-center">Koordinat</th>
                    <th class="p-2 text-center">Panjang</th>
                    <th class="p-2 text-center">Lebar</th>
                    <th class="p-2 text-center">Eksisting</th>
                    <th class="p-2 text-center">Kondisi</th>
                    <th class="p-2 text-center">Jenis Jalan</th>
                    <th class="p-2 text-center">Keterangan</th>
                    <th class="p-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody id="polylineTableBody">
                @if(isset($polylines['ruasjalan']) && is_array($polylines['ruasjalan']))
                    @foreach ($polylines['ruasjalan'] as $polyline)
                    <tr class="hover:bg-gray-100">
                        <td class="p-2 text-center">{{ $polyline['nama_ruas'] }}</td>
                        <td class="p-2 text-center truncate" title="{{ $polyline['paths'] }}">{{ Str::limit($polyline['paths'], 30) }}</td>
                        <td class="p-2 text-center">{{ $polyline['panjang'] }}</td>
                        <td class="p-2 text-center">{{ $polyline['lebar'] }}</td>
                        <td class="p-2 text-center">{{ $polyline['eksisting_id'] }}</td>
                        <td class="p-2 text-center">{{ $polyline['kondisi_id'] }}</td>
                        <td class="p-2 text-center">{{ $polyline['jenisjalan_id'] }}</td>
                        <td class="p-2 text-center">{{ $polyline['keterangan'] }}</td>
                        <td class="p-2 text-center flex justify-center space-x-2">
                            <button type="button" class="btn btn-warning btn-xs btn-detail" data-id="{{ $polyline['id'] }}">Detail</button>
                            <form action="{{ route('polyline.edit', $polyline['id']) }}" method="GET" >
                                @csrf
                                @method('GET')
                                <a href="{{ route('polyline.edit', $polyline['id']) }}" class="btn btn-accent btn-xs">Edit</a>
                            </form>
                            

                            <form action="{{ route('polyline.destroy', $polyline['id']) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class=" btn btn-error btn-xs btn-delete" data-id="{{ $polyline['id'] }}">Delete</button>
                            </form>   
                            
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<meta name="api-token" content="{{ csrf_token() }}">
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

        async function fetchRuasJalan() {
            const response = await axios.get(api_main_url + "ruasjalan", { headers });
            if (response.status !== 200) {
                throw new Error('Failed to fetch ruas jalan data: ' + response.statusText);
            }
            console.log('API Response:', response.data);
            return response.data;
        }

        function parseCoordinates(coords) {
            // Pisahkan string koordinat dengan tanda '-'
            const coordinatePairs = coords.split(' ');
            // Buat array untuk menampung koordinat yang telah diurai
            let coordinates = [];
            // Iterasi setiap pasangan koordinat
            coordinatePairs.forEach(pair => {
                // Pisahkan latitutde dan longitude dengan tanda ','
                const [lat, lng] = pair.split(',').map(parseFloat);
                // Jika latitutde dan longitude valid, tambahkan ke array coordinates
                if (!isNaN(lat) && !isNaN(lng)) {
                    coordinates.push([lat, lng]);
                } else {
                    console.warn('Invalid coordinate pair:', pair);
                }
            });
            return coordinates;
        }


        function drawPolylines(polylineData) {
            console.log('Drawing polylines:', polylineData);
            polylineData.forEach(polyline => {
                if (!polyline.paths) {
                    console.warn('Polyline paths missing:', polyline);
                    return;
                }
                let coordinates = parseCoordinates(polyline.paths);
                if (coordinates.length === 0) {
                    console.warn('No valid coordinates for polyline:', polyline);
                    return;
                }
                const line = L.polyline(coordinates, { color: 'red' }).addTo(map);
                map.fitBounds(line.getBounds());
            });
        }


        try {
            console.log('Fetching data from API...');
            const data_ruas = await fetchRuasJalan();
            console.log('Data Ruas:', data_ruas);

            if (typeof map === 'undefined') {
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
            }

            const polylinesArray = [];
            if (Array.isArray(data_ruas.ruasjalan)) {
                data_ruas.ruasjalan.forEach(ruas => {
                    if (typeof ruas === 'object' && ruas !== null && 'nama_ruas' in ruas) {
                        let coordinates = parseCoordinates(ruas.paths);
                        if (coordinates.length === 0) {
                            console.warn('No valid coordinates for ruas:', ruas);
                            return;
                        }
                        const line = L.polyline(coordinates, { color: 'red' }).addTo(map);
                        polylinesArray.push(line);
                    } else {
                        console.error('Invalid ruas data:', ruas);
                    }
                });
            }

            if (polylinesArray.length > 0) {
                const group = new L.featureGroup(polylinesArray);
                map.fitBounds(group.getBounds());
            }
        } catch (error) {
            console.error('Error fetching data:', error);
        }


        async function deletePolyline(id) {
            const confirmResult = await Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak akan dapat mengembalikan tindakan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (confirmResult.isConfirmed) {
                try {
                    const response = await axios.delete(api_main_url + "ruasjalan/" + id, { headers });
                    if (response.status !== 200) {
                        throw new Error('Failed to delete ruas jalan data: ' + response.statusText);
                    }
                    console.log('Ruas jalan deleted:', id);
                    // Hapus baris data dari tabel
                    document.getElementById('polylineTableBody').querySelector(`[data-id="${id}"]`).remove();
                    Swal.fire(
                        'Deleted!',
                        'Data ruas jalan telah dihapus.',
                        'success'
                    );
                } catch (error) {
                    console.error('Error deleting ruas jalan data:', error);
                    Swal.fire(
                        'Error!',
                        'Gagal menghapus data ruas jalan.',
                        'error'
                    );
                }
            }
        }


        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                const polylineId = event.target.getAttribute('data-id');
                deletePolyline(polylineId);
            });
        });

        const detailButtons = document.querySelectorAll('.btn-detail');
        const popupInfo = document.getElementById('popupInfo');
        const popupTitle = document.getElementById('popupTitle');
        const popupContent = document.getElementById('popupContent');
        const closePopupButton = document.getElementById('closePopup');

        detailButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                const polylineId = event.target.getAttribute('data-id');
                fetchPolylineDetails(polylineId);
            });
        });

        function fetchPolylineDetails(id) {
            axios.get(api_main_url + "ruasjalan/" + id, { headers })
                .then(response => {
                    const polylineData = response.data;
                    // Isi konten popup dengan data yang diperoleh
                    popupTitle.innerText = polylineData.nama_ruas;
                    popupContent.innerHTML = `
                        <p><b>Koordinat:</b> ${polylineData.paths}</p>
                        <p><b>Panjang:</b> ${polylineData.panjang}</p>
                        <p><b>Lebar:</b> ${polylineData.lebar}</p>
                        <p><b>Eksisting:</b> ${polylineData.eksisting_id}</p>
                        <p><b>Kondisi:</b> ${polylineData.kondisi_id}</p>
                        <p><b>Jenis Jalan:</b> ${polylineData.jenisjalan_id}</p>
                        <p><b>Keterangan:</b> ${polylineData.keterangan}</p>
                    `;
                    // Tampilkan popup
                    popupInfo.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching polyline data:', error);
                    Swal.fire(
                        'Error!',
                        'Gagal mendapatkan data ruas jalan.',
                        'error'
                    );
                });
        }

    

    });
</script>
@endpush