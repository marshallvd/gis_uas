<nav id="sidebarMenu" class="sidebar d-lg-block bg-oldmoney-darkest-charcoal text-white collapse" data-simplebar>
    <div class="sidebar-inner px-4 pt-3">
        <ul class="nav flex-column pt-3 pt-md-0">
            <li class="nav-item">
                <a href="" class="nav-link d-flex align-items-center">
                    <span class="sidebar-icon">
                        <img src="" height="20" width="20">
                    </span>
                    <span class="mt-1 ms-1 sidebar-text">SI - GIS</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link">
                    <span class="sidebar-icon">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <span class="nav-link collapsed d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#submenu-data">
                    <span>
                        <span class="sidebar-icon">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                                <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <span class="sidebar-text">Master Data</span>
                    </span>
                    <span class="link-arrow">
                        <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                </span>
                <div class="multi-level collapse" role="list" id="submenu-data" aria-expanded="false">
                    <ul class="flex-column nav">
                        <li class="nav-item">
                            <a class="nav-link" href="">
                                <span class="sidebar-text">Spot</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li role="separator" class="dropdown-divider mt-4 mb-3 border-gray-700"></li>
        </ul>
    </div>
</nav>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        #map {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0; /* Ensure the map is behind other content */
        }

        .custom-card {
            background-color: #ffffff; /* Set card background to white */
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
            margin-bottom: 10px;
        }

        .custom-card-header {
            padding: 10px;
            border-radius: 10px 10px 0px 0px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e2e8f0;
        }

        .custom-card-body {
            padding: 10px;
        }
    </style>
</head>
<body class="bg-gray-200">
<div id="map"></div> <!-- Move map outside and above other elements -->

<div class="container mb-4 mt-2 ml-2 "> <!-- Add relative positioning and z-index to the container -->
    <div class="flex flex-wrap">
        <div class="w-full md:w-1/3 p-4">
            <div class="custom-card">
                <div class="custom-card-header">
                    <h1 class="text-xl font-bold text-gray-900">Tambah Data Jalan</h1>
                </div>
                
                <div class="custom-card-body">
                    <form enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div class="form-control">
                                <label class="label" for="province">
                                    <span class="label-text text-gray-700"><b>Pilih Provinsi</b></span>
                                </label>
                                <select class="select select-bordered w-full border-gray-300 rounded-lg shadow-sm" id="province" name="province" required>
                                    <option value="">Pilih Provinsi</option>
                                </select>
                            </div>
                            
                            <div class="form-control">
                                <label class="label" for="kabupaten">
                                    <span class="label-text text-gray-700"><b>Pilih Kabupaten</b></span>
                                </label>
                                <select class="select select-bordered w-full border-gray-300 rounded-lg shadow-sm" id="kabupaten" name="kabupaten" required>
                                    <option value="">Pilih Kabupaten</option>
                                </select>
                            </div>
                            
                            <div class="form-control">
                                <label class="label" for="kecamatan">
                                    <span class="label-text text-gray-700"><b>Pilih Kecamatan</b></span>
                                </label>
                                <select class="select select-bordered w-full border-gray-300 rounded-lg shadow-sm" id="kecamatan" name="kecamatan" required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                            
                            <div class="form-control">
                                <label class="label" for="desa">
                                    <span class="label-text text-gray-700"><b>Pilih Desa</b></span>
                                </label>
                                <select class="select select-bordered w-full border-gray-300 rounded-lg shadow-sm" id="desa" name="desa" required>
                                    <option value="">Pilih Desa</option>
                                </select>
                            </div>
                            
                            <div class="form-control">
                                <label class="label" for="nama_ruas">
                                    <span class="label-text text-gray-700"><b>Nama Ruas</b></span>
                                </label>
                                <input type="text" class="input input-bordered w-full border-gray-300 rounded-lg shadow-sm" id="nama_ruas" name="nama_ruas" required />
                            </div>

                            <div class="form-control">
                                <label class="label" for="lebar">
                                    <span class="label-text text-gray-700"><b>Lebar Ruas</b></span>
                                </label>
                                <input type="text" class="input input-bordered w-full border-gray-300 rounded-lg shadow-sm" id="lebar" name="lebar" required />
                            </div>

                            <div class="form-control">
                                <label class="label" for="kode_ruas">
                                    <span class="label-text text-gray-700"><b>Kode Ruas</b></span>
                                </label>
                                <input type="text" class="input input-bordered w-full border-gray-300 rounded-lg shadow-sm" id="kode_ruas" name="kode_ruas" required />
                            </div>

                            <div class="form-control">
                                <label class="label" for="eksisting">
                                    <span class="label-text text-gray-700"><b>Eksisting</b></span>
                                </label>
                                <select class="select select-bordered w-full border-gray-300 rounded-lg shadow-sm" id="eksisting" name="eksisting" required>
                                    <option value="">Pilih Material</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label" for="kondisi">
                                    <span class="label-text text-gray-700"><b>Kondisi</b></span>
                                </label>
                                <select class="select select-bordered w-full border-gray-300 rounded-lg shadow-sm" id="kondisi" name="kondisi" required>
                                    <option value="">Pilih Kondisi</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label" for="jenis_jalan">
                                    <span class="label-text text-gray-700"><b>Jenis Jalan</b></span>
                                </label>
                                <select class="select select-bordered w-full border-gray-300 rounded-lg shadow-sm" id="jenis_jalan" name="jenis_jalan" required>
                                    <option value="">Pilih Jenis</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label" for="keterangan">
                                    <span class="label-text text-gray-700"><b>Keterangan</b></span>
                                </label>
                                <input type="text" class="input input-bordered w-full border-gray-300 rounded-lg shadow-sm" id="keterangan" name="keterangan" required />
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg">Tambah Jalan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<meta name="api-token" content="{{ session('token') }}">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        var map = L.map('map').setView([-8.65, 115.22], 10); // Contoh koordinat untuk Bali
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Add Leaflet Draw
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);
        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: drawnItems
            }
        });
        map.addControl(drawControl);

        map.on(L.Draw.Event.CREATED, function (event) {
            var layer = event.layer;
            drawnItems.addLayer(layer);
        });

        // Fetch options for province, kabupaten, kecamatan, and desa
        const token = document.querySelector('meta[name="api-token"]').getAttribute('content');

        const provinceSelect = document.getElementById('province');
        const kabupatenSelect = document.getElementById('kabupaten');
        const kecamatanSelect = document.getElementById('kecamatan');
        const desaSelect = document.getElementById('desa');

        fetch('https://gisapis.manpits.xyz/api/mregion', {
            headers: {
                Authorization: Bearer ${token},
            }
        })
        .then(response => response.json())
        .then(data => {
            provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
            data.provinsi.forEach(province => {
                const option = document.createElement('option');
                option.value = province.id;
                option.textContent = province.provinsi;
                provinceSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching provinces:', error);
        });

        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
            
            if (provinceId) {
                fetch(https://gisapis.manpits.xyz/api/kabupaten/${provinceId}, {
                    headers: {
                        Authorization: Bearer ${token},
                    }
                })
                .then(response => response.json())
                .then(data => {
                    kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
                    data.kabupaten.forEach(kabupaten => {
                        const option = document.createElement('option');
                        option.value = kabupaten.id;
                        option.textContent = kabupaten.value;
                        kabupatenSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching kabupaten:', error);
                });
            }
        });

        kabupatenSelect.addEventListener('change', function() {
            const kabupatenId = this.value;
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            if (kabupatenId) {
                fetch(https://gisapis.manpits.xyz/api/kecamatan/${kabupatenId}, {
                    headers: {
                        Authorization: Bearer ${token},
                    }
                })
                .then(response => response.json())
                .then(data => {
                    kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                    data.kecamatan.forEach(kecamatan => {
                        const option = document.createElement('option');
                        option.value = kecamatan.id;
                        option.textContent = kecamatan.value;
                        kecamatanSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching kecamatan:', error);
                });
            }
        });

        kecamatanSelect.addEventListener('change', function() {
            const kecamatanId = this.value;
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            if (kecamatanId) {
                fetch(https://gisapis.manpits.xyz/api/desa/${kecamatanId}, {
                    headers: {
                        Authorization: Bearer ${token},
                    }
                })
                .then(response => response.json())
                .then(data => {
                    desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
                    data.desa.forEach(desa => {
                        const option = document.createElement('option');
                        option.value = desa.id;
                        option.textContent = desa.value;
                        desaSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching desa:', error);
                });
            }
        });
    });
</script>
</body>
</html>