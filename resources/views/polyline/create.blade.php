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
            height: 1050px; 
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
        }

        .custom-card-body {
            padding: 20px; /* Ruang dalam body card */
        }

        .custom-label {
            font-weight: bold; /* Tulisan tebal pada label */
        }
        
    </style>
@endsection

@section('contents')
<div class="container mx-auto my-8">
    <div class="flex flex-wrap">
        <div class="w-full md:w-1/3 p-4">
            <div class="custom-card">
                <div class="custom-card-header">
                    <h1 class="text-xl font-bold">Tambah Data Jalan</h1>
                </div>
                
                <div class="custom-card-body">
                    <form action="{{ route('polyline.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div class="form-control">
                                <label class="label" for="province">
                                    <span class="label-text"><b>Pilih Provinsi</b></span>
                                </label>
                                <select class="select select-bordered w-full" id="province" name="province" required>
                                    <option value="">Pilih Provinsi</option>
                                </select>
                            </div>
                            
                            <div class="form-control">
                                <label class="label" for="kabupaten">
                                    <span class="label-text"><b>Pilih Kabupaten</b></span>
                                </label>
                                <select class="select select-bordered w-full" id="kabupaten" name="kabupaten" required>
                                    <option value="">Pilih Kabupaten</option>
                                </select>
                            </div>
                            
                            <div class="form-control">
                                <label class="label" for="kecamatan">
                                    <span class="label-text"><b>Pilih Kecamatan</b></span>
                                </label>
                                <select class="select select-bordered w-full" id="kecamatan" name="kecamatan" required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                            
                            <div class="form-control">
                                <label class="label" for="desa">
                                    <span class="label-text"><b>Pilih Desa</b></span>
                                </label>
                                <select class="select select-bordered w-full" id="desa" name="desa" required>
                                    <option value="">Pilih Desa</option>
                                </select>
                            </div>
                            
                            <div class="form-control">
                                <label class="label" for="nama_ruas">
                                    <span class="label-text"><b>Nama Ruas</b></span>
                                </label>
                                <input type="text" class="input input-bordered w-full" id="nama_ruas" name="nama_ruas" required />
                            </div>

                            <div class="form-control">
                                <label class="label" for="lebar">
                                    <span class="label-text"><b>Lebar Ruas</b></span>
                                </label>
                                <input type="text" class="input input-bordered w-full" id="lebar" name="lebar" required />
                            </div>

                            <div class="form-control">
                                <label class="label" for="kode_ruas">
                                    <span class="label-text"><b>Kode Ruas</b></span>
                                </label>
                                <input type="text" class="input input-bordered w-full" id="kode_ruas" name="kode_ruas" required />
                            </div>

                            <div class="form-control">
                                <label class="label" for="eksisting">
                                    <span class="label-text"><b>Eksisting</b></span>
                                </label>
                                <select class="select select-bordered w-full" id="eksisting" name="eksisting" required>
                                    <option value="">Pilih Material</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label" for="kondisi">
                                    <span class="label-text"><b>Kondisi</b></span>
                                </label>
                                <select class="select select-bordered w-full" id="kondisi" name="kondisi" required>
                                    <option value="">Pilih Kondisi</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label" for="jenis_jalan">
                                    <span class="label-text"><b>Jenis Jalan</b></span>
                                </label>
                                <select class="select select-bordered w-full" id="jenis_jalan" name="jenis_jalan" required>
                                    <option value="">Pilih Jenis</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label" for="keterangan">
                                    <span class="label-text"><b>Keterangan</b></span>
                                </label>
                                <input type="text" class="input input-bordered w-full" id="keterangan" name="keterangan" required />
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-full">Tambah Jalan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-full md:w-2/3 p-4">
            <div class="custom-card">
                <div class="custom-card-header">
                    <h1 class="text-xl font-bold">Peta</h1>
                </div>
                <div class="custom-card-body">
                    <div id="map"></div>
                    
                </div>
                <div class="container mx-auto mb-8 text-center">
                    <a href="{{ route('polyline.index') }}" class="btn btn-secondary">Kembali</a>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>



<meta name="api-token" content="{{ session('token') }}">
@endsection

@push('javascript')
<script src="{{ asset('js/home.js') }}"></script>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const token = document.querySelector('meta[name="api-token"]').getAttribute('content');
        console.log('Token:', token);

        const provinceSelect = document.getElementById('province');
        const kabupatenSelect = document.getElementById('kabupaten');
        const kecamatanSelect = document.getElementById('kecamatan');
        const desaSelect = document.getElementById('desa');
        const eksistingSelect = document.getElementById('eksisting');
        const kondisiSelect = document.getElementById('kondisi');
        const jenisJalanSelect = document.getElementById('jenis_jalan');

        fetch('https://gisapis.manpits.xyz/api/mregion', {
            headers: {
                Authorization: `Bearer ${token}`,
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Provinces:', data.provinsi); // Log data provinces for debugging
            
            // Clear existing options
            provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';

            const provinceSet = new Set();

            data.provinsi.forEach(province => {
                if (!provinceSet.has(province.id)) {
                    provinceSet.add(province.id);
                    const option = document.createElement('option');
                    option.value = province.id;
                    option.textContent = province.provinsi;
                    provinceSelect.appendChild(option);
                }
            });
        })
        .catch(error => {
            console.error('Error fetching provinces:', error); // Log error
        });

        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
            
            if (provinceId) {
                fetch(`https://gisapis.manpits.xyz/api/kabupaten/${provinceId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Kabupaten:', data.kabupaten); // Log data kabupaten for debugging
                    
                    // Clear existing options
                    kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';

                    data.kabupaten.forEach(kabupaten => {
                        const option = document.createElement('option');
                        option.value = kabupaten.id;
                        option.textContent = kabupaten.value;
                        kabupatenSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching kabupaten:', error); // Log error
                });
            }
        });

        kabupatenSelect.addEventListener('change', function() {
            const kabupatenId = this.value;
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
            
            if (kabupatenId) {
                fetch(`https://gisapis.manpits.xyz/api/kecamatan/${kabupatenId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Kecamatan:', data.kecamatan); // Log data kecamatan for debugging
                    kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                    data.kecamatan.forEach(kecamatan => {
                        const option = document.createElement('option');
                        option.value = kecamatan.id;
                        option.textContent = kecamatan.value;
                        kecamatanSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching kecamatan:', error); // Log error
                });
            }
        });

        kecamatanSelect.addEventListener('change', function() {
            const kecamatanId = this.value;
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
            
            if (kecamatanId) {
                fetch(`https://gisapis.manpits.xyz/api/desa/${kecamatanId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Desa:', data.desa); // Log data desa for debugging
                    desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
                    data.desa.forEach(desa => {
                        const option = document.createElement('option');
                        option.value = desa.id;
                        option.textContent = desa.value;
                        desaSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching desa:', error); // Log error
                });
            }
        });

        // Mendapatkan data eksisting dari API
    fetch('https://gisapis.manpits.xyz/api/eksisting', {
        headers: {
            Authorization: `Bearer ${token}`,
        }
    })
    .then(response => response.json())
    console.log(response.data) 
    .then(data => {
        console.log('Eksisting:', data.name); // Log data eksisting untuk debugging
        
        // Mengisi dropdown eksisting dengan data dari API
        eksistingSelect.innerHTML = '<option value="">Pilih Material</option>';
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nama;
            eksistingSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error fetching eksisting:', error); // Log error
    });

    // Mendapatkan data ruas jalan dari API
    fetch('https://gisapis.manpits.xyz/api/ruasjalan', {
        headers: {
            Authorization: `Bearer ${token}`,
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Ruas Jalan:', data.nama_ruas); // Log data ruas jalan untuk debugging
        
        // Mengisi dropdown kondisi dengan data dari API
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nama_ruas;
            ruasJalanSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error fetching ruas jalan:', error); // Log error
    });

    // Mendapatkan data jenis jalan dari API
    fetch('https://gisapis.manpits.xyz/api/jenisjalan', {
        headers: {
            Authorization: `Bearer ${token}`,
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Jenis Jalan:', data); // Log data jenis jalan untuk debugging
        
        // Mengisi dropdown jenis jalan dengan data dari API
        jenisJalanSelect.innerHTML = '<option value="">Pilih Jenis</option>';
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nama;
            jenisJalanSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error fetching jenis jalan:', error); // Log error
    });

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

    map.on(L.Draw.Event.CREATED, function (event) {
        var layer = event.layer;
        drawnItems.addLayer(layer);
        console.log('Layer created:', layer); // Log created layer for debugging

        if (layer instanceof L.Polyline) {
            console.log('Polyline coordinates:', layer.getLatLngs());
        } else if (layer instanceof L.Polygon) {
            console.log('Polygon coordinates:', layer.getLatLngs());
        }
    });

    

</script>

@endpush
