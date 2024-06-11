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
            <h1 class="text-xl font-bold">Tambah Data Jalan</h1>
            <a href="{{ route('polyline.index') }}" class="btn btn-outline btn-secondary">Kembali</a>
        </div>
        <div class="custom-card-body">
            <div id="map"></div>
        </div>
    </div>

    <div class="custom-card">
        <div class="custom-card-body">
            <form action="{{ route('polyline.store') }}" method="POST" enctype="multipart/form-data" id="form" name="form">
                @csrf
                <div class="form-section">
                    <!-- Bagian Pertama -->
                    <div>
                        <h2 class="text-lg font-semibold mb-2  text-center">Informasi Ruas Jalan</h2>
                        <!-- Nama Ruas -->
                        <div class="form-control">
                            <label class="label" for="nama_ruas">
                                <span class="label-text"><b>Nama Ruas</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="nama_ruas" name="nama_ruas" required />
                        </div>
                        <!-- Lebar Ruas -->
                        <div class="form-control">
                            <label class="label" for="lebar">
                                <span class="label-text"><b>Lebar Ruas</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="lebar" name="lebar" required />
                        </div>
                        <!-- Kode Ruas -->
                        <div class="form-control">
                            <label class="label" for="kode_ruas">
                                <span class="label-text"><b>Kode Ruas</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="kode_ruas" name="kode_ruas" required />
                        </div>
                        <!-- Keterangan -->
                        <div class="form-control">
                            <label class="label" for="keterangan">
                                <span class="label-text"><b>Keterangan</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="keterangan" name="keterangan" required />
                        </div>
                    </div>

                    <!-- Bagian Kedua -->
                    <div>
                        <h2 class="text-lg font-semibold mb-2  text-center">Lokasi Ruas Jalan</h2>
                        <!-- Dropdown Provinsi -->
                        <div class="form-control">
                            <label class="label" for="province">
                                <span class="label-text"><b>Pilih Provinsi</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="province" name="province" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>
                        <!-- Dropdown Kabupaten -->
                        <div class="form-control">
                            <label class="label" for="kabupaten">
                                <span class="label-text"><b>Pilih Kabupaten</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="kabupaten" name="kabupaten" required>
                                <option value="">Pilih Kabupaten</option>
                            </select>
                        </div>
                        <!-- Dropdown Kecamatan -->
                        <div class="form-control">
                            <label class="label" for="kecamatan">
                                <span class="label-text"><b>Pilih Kecamatan</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="kecamatan" name="kecamatan" required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                        <!-- Dropdown Desa -->
                        <div class="form-control">
                            <label class="label" for="desa">
                                <span class="label-text"><b>Pilih Desa</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="desa" name="desa" required>
                                <option value="">Pilih Desa</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bagian Ketiga -->
                    <div>
                        <h2 class="text-lg font-semibold mb-2  text-center">Detail Ruas Jalan</h2>
                        <!-- Dropdown Eksisting -->
                        <div class="form-control">
                            <label class="label" for="eksisting">
                                <span class="label-text"><b>Eksisting</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="eksisting" name="eksisting" required>
                                <option value="">Pilih Material</option>
                            </select>
                        </div>
                        <!-- Dropdown Kondisi -->
                        <div class="form-control">
                            <label class="label" for="kondisi">
                                <span class="label-text"><b>Kondisi</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="kondisi" name="kondisi" required>
                                <option value="">Pilih Kondisi</option>
                            </select>
                        </div>
                        <!-- Dropdown Jenis Jalan -->
                        <div class="form-control">
                            <label class="label" for="jenis_jalan">
                                <span class="label-text"><b>Jenis Jalan</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="jenis_jalan" name="jenis_jalan" required>
                                <option value="">Pilih Jenis</option>
                            </select>
                        </div>

                        <!-- Latlng (Koordinat) -->
                        <div class="form-control">
                            <label class="label" for="latlng">
                                <span class="label-text"><b>Latlng</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="latlng" name="latlng" required />
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit dan Reset -->
                <div class="flex justify-between">
                    <button type="submit" class="btn btn-primary w-1/2 mt-4">Tambah Jalan</button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


<script>
    // Fungsi untuk menghitung panjang garis polyline
    function calculateLength(latlngs) {
        let length = 0;
        for (let i = 0; i < latlngs.length - 1; i++) {
            length += latlngs[i].distanceTo(latlngs[i + 1]);
        }
        return length;
    }


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
    fetch('https://gisapis.manpits.xyz/api/meksisting', {
        headers: {
            Authorization: `Bearer ${token}`,
        }
    })
    .then(response => response.json())
    // console.log(response.data)
    .then(data => {
        
        console.log('Eksisting:', data.eksisting); // Log data eksisting untuk debugging
        
        // Mengisi dropdown eksisting dengan data dari API
        eksistingSelect.innerHTML = '<option value="">Pilih Material</option>';
        data.eksisting.forEach(eksisting => {
            const option = document.createElement('option');
            option.value = eksisting.id;
            option.textContent = eksisting.eksisting;
            eksistingSelect.appendChild(option);
        });

    })
    .catch(error => {
        console.error('Error fetching eksisting:', error); // Log error
    });

    
    // Mendapatkan data jenis jalan dari API
    fetch('https://gisapis.manpits.xyz/api/mjenisjalan', {
        headers: {
            Authorization: `Bearer ${token}`,
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Jenis Jalan:', data.jenisjalan); // Log data jenis jalan untuk debugging
        
        // Mengisi dropdown jenis jalan dengan data dari API
        jenisJalanSelect.innerHTML = '<option value="">Pilih Jenis</option>';
        data.eksisting.forEach(eksisting => {
            const option = document.createElement('option');
            option.value = eksisting.id;
            option.textContent = eksisting.jenisjalan;
            jenisJalanSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error fetching jenis jalan:', error); // Log error
    });


    fetch('https://gisapis.manpits.xyz/api/mkondisi', {
        headers: {
            Authorization: `Bearer ${token}`,
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Kondisi:', data.kondisi); // Log data jenis jalan untuk debugging
        
        // Mengisi dropdown jenis jalan dengan data dari API
        kondisiSelect.innerHTML = '<option value="">Kondisi</option>';
        data.eksisting.forEach(eksisting => {
            const option = document.createElement('option');
            option.value = eksisting.id;
            option.textContent = eksisting.kondisi;
            kondisiSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error fetching jenis jalan:', error); // Log error
    });

    document.getElementById('form').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah formulir dikirimkan secara langsung

        // Mengumpulkan data dari formulir
        const formData = {
            paths: document.getElementById('latlng').value,
            desa_id: document.getElementById('desa').value,
            kode_ruas: document.getElementById('kode_ruas').value,
            nama_ruas: document.getElementById('nama_ruas').value,
            panjang: calculateLength(drawnItems.getLayers()[0].getLatLngs()), // Menggunakan fungsi calculateLength untuk menghitung panjang
            lebar: parseFloat(document.getElementById('lebar').value),
            eksisting_id: parseInt(document.getElementById('eksisting').value),
            kondisi_id: parseInt(document.getElementById('kondisi').value),
            jenisjalan_id: parseInt(document.getElementById('jenis_jalan').value),
            keterangan: document.getElementById('keterangan').value
        };

        console.log('Form data to be sent:', formData); // Tambahkan log ini

        const token = document.querySelector('meta[name="api-token"]').getAttribute('content');

        // Membuat permintaan HTTP POST ke API
        fetch('https://gisapis.manpits.xyz/api/ruasjalan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            console.log('Raw response:', response); // Log raw response
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json().then(data => ({ status: response.status, body: data }));
        })
        // .then(({ status, body }) => {
        //     if (status !== 200) {
        //         console.error('Error data:', body); // Log error data
        //         throw new Error(body.message || 'Gagal menyimpan data.');
        //     }
        //     console.log('Data berhasil disimpan:', body);
        //     // alert('Data berhasil disimpan.');
        //     // Redirect to index page after successful data submission
        //     window.location.href = "{{ route('polyline.index') }}";
        // })
        // .catch(error => {
        //     console.error('Terjadi kesalahan:', error);
        //     if (error.message.includes('Unexpected token')) {
        //         console.error('Respons API tidak valid:', error.message);
        //     } else if (error.message.includes('HTTP error')) {
        //         console.error('Server mengembalikan status error:', error.message);
        //     }
        //     alert(`Terjadi kesalahan: ${error.message}`);
        // });
        .then(({ status, body }) => {
            if (status !== 200) {
                console.error('Error data:', body); // Log error data
                throw new Error(body.message || 'Gagal menyimpan data.');
            }
            console.log('Data berhasil disimpan:', body);
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: 'Data berhasil disimpan.'
            }).then(() => {
                // Redirect to index page after successful data submission
                window.location.href = "{{ route('polyline.index') }}";
            });
        })
        .catch(error => {
            console.error('Terjadi kesalahan:', error);
            if (error.message.includes('Unexpected token')) {
                console.error('Respons API tidak valid:', error.message);
            } else if (error.message.includes('HTTP error')) {
                console.error('Server mengembalikan status error:', error.message);
            }
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: `Terjadi kesalahan: ${error.message}`
            });
        });

    });



    var map = L.map('map').setView([-8.409518, 115.188919], 11);

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

            var latlngs;
            if (layer instanceof L.Polyline) {
                latlngs = layer.getLatLngs();
            } else if (layer instanceof L.Polygon) {
                latlngs = layer.getLatLngs()[0]; // outer ring
            }

            var latlngString = latlngs.map(function(latlng) {
                return `${latlng.lat},${latlng.lng}`;
            }).join(' ');

            document.getElementById('latlng').value = latlngString;

            // Calculate the length of the polyline
            var length = calculateLength(latlngs);
            console.log('Length:', length);

            alert(`Panjang Polyline: ${length.toFixed(2)} meters`);
        });

        map.on(L.Draw.Event.EDITED, function (event) {
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
                return `${latlng.lat},${latlng.lng}`;
            }).join(' ');

            document.getElementById('latlng').value = latlngString;

            // Calculate the length of the polyline
            var length = calculateLength(latlngs);
            console.log('Length:', length);

            alert(`Panjang Polyline: ${length.toFixed(2)} meters`);
        });


    document.getElementById('form').addEventListener('reset', function() {
        // Menghapus semua layer dari drawnItems ketika tombol reset ditekan
        drawnItems.clearLayers();
        // Reset koordinat
        document.getElementById('latlng').value = '';
    });

    

    
});
</script>

@endpush
