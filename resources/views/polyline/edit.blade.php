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
            <form action="{{ route('polyline.update', $data_ruas_jalan->id ?? '') }}" method="POST" enctype="multipart/form-data" id="form" name="form">
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
                            {{-- <input type="text" class="input input-bordered w-full border-gray-300 rounded-lg shadow-sm" id="nama_ruas" name="nama_ruas" value="{{$data_ruas_jalan->nama_ruas['nama_ruas'] }}" required /> --}}
                            
                            <input type="text" class="input input-bordered w-full" id="nama_ruas" name="nama_ruas" value="{{ old('namaRuas', $data_ruas_jalan->namaRuas ?? '') }}" required />
                        </div>
                        <!-- Lebar Ruas -->
                        <div class="form-control">
                            <label class="label" for="lebar">
                                <span class="label-text"><b>Lebar Ruas</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="lebar" name="lebar" value="{{ $data_ruas_jalan->lebar ?? '' }}" required />
                            
                        </div>
                        <!-- Kode Ruas -->
                        <div class="form-control">
                            <label class="label" for="kode_ruas">
                                <span class="label-text"><b>Kode Ruas</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="kode_ruas" name="kode_ruas" value="{{ $data_ruas_jalan->kode_ruas ?? '' }}" required />
                        </div>
                        <!-- Keterangan -->
                        <div class="form-control">
                            <label class="label" for="keterangan">
                                <span class="label-text"><b>Keterangan</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="keterangan" name="keterangan" value="{{ $data_ruas_jalan->keterangan ?? '' }}" required />
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
                                @foreach($provinces as $province)
                                    <option value="{{ $province['id'] }}">{{ $province['provinsi'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        

                        <!-- Dropdown Kabupaten -->
                        {{-- @php
                            dd($kabupatens);
                        @endphp --}}
                        <div cla
                        <div class="form-control">
                            <label class="label" for="kabupaten">
                                <span class="label-text"><b>Pilih Kabupaten</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="kabupaten" name="kabupaten" required>
                                <option value="">Pilih Kabupaten</option>
                                @foreach($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten['id'] }}">{{ $kabupaten['kabupaten'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Dropdown Kecamatan -->
                        {{-- @php
                            dd($kecamatans);
                        @endphp --}}
                        <div cla
                        <div class="form-control">
                            <label class="label" for="kecamatan">
                                <span class="label-text"><b>Pilih Kecamatan</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="kecamatan" name="kecamatan" required>
                                <option value="">Pilih Kecamatan</option>
                                @foreach($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan['id'] }}">{{ $kecamatan['kecamatan'] }}</option>
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
                                @foreach($desas as $desa)
                                    <option value="{{ $desa['id'] }}">{{ $desa['desa'] }}</option>
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
                                @foreach($eksistings['eksisting'] as $eksisting)
                                    <option value="{{ $eksisting['id'] }}" {{ $data_ruas_jalan->ruasjalan->eksisting_id == $eksisting['id'] ? 'selected' : '' }}>
                                        {{ $eksisting['eksisting'] }}
                                    </option>
                                @endforeach


                            </select>
                        </div>
                        <!-- Dropdown Kondisi -->
                        {{-- @php
                            dd($data_ruas_jalan);
                        @endphp --}}
                        <div class="form-control">
                            <label class="label" for="kondisi">
                                <span class="label-text"><b>Kondisi</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="kondisi" name="kondisi" required>
                                <option value="">Pilih Kondisi</option>
                                @foreach($kondisis['eksisting'] as $kondisi)
                                    <option value="{{ $kondisi['id'] }}" {{ $data_ruas_jalan->ruasjalan->kondisi_id == $kondisi['id'] ? 'selected' : '' }}>
                                        {{ $kondisi['kondisi'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Dropdown Jenis Jalan -->
                        <div class="form-control">
                            <label class="label" for="jenis_jalan">
                                <span class="label-text"><b>Jenis Jalan</b></span>
                            </label>
                            <select class="select select-bordered w-full" id="jenis_jalan" name="jenis_jalan" required>
                                <option value="{{ $kondisi['id'] }}" {{ isset($data_ruas_jalan->kondisi_id) && $data_ruas_jalan->kondisi_id == $kondisi['id'] ? 'selected' : '' }}>{{ $kondisi['kondisi'] }}</option>
                            </select>
                        </div>

                        <!-- Latlng (Koordinat) -->
                        <div class="form-control">
                            <label class="label" for="latlng">
                                <span class="label-text"><b>Latlng</b></span>
                            </label>
                            <input type="text" class="input input-bordered w-full" id="latlng" name="latlng" value="{{ $data_ruas_jalan->paths ?? '' }}" required />
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
    document.addEventListener('DOMContentLoaded', function () {
        const token = document.querySelector('meta[name="api-token"]').getAttribute('content');
        const api_main_url = 'https://gisapis.manpits.xyz/api/';
        const map = L.map('map').setView([-8.409518, 115.188919], 11);

        // Adding basemaps
        const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        const Esri_World = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        const baseLayers = {
            "OSM Tiles": tiles,
            "ESRI World Imagery": Esri_World,
        };

        L.control.layers(baseLayers).addTo(map);

        // Parse koordinat polyline dan tampilkan di peta
        const polylineCoords = {!! json_encode($data_ruas_jalan->ruasjalan->paths) !!}.split(' ').map(coord => {
            const [lat, lng] = coord.split(',').map(parseFloat);
            return [lat, lng];
        });


        const polyline = L.polyline(polylineCoords, { color: 'red' }).addTo(map);
        map.fitBounds(polyline.getBounds());

        // Event listener untuk dropdown provinsi
        document.getElementById('province').addEventListener('change', function() {
            const provinceId = this.value;
            const kabupatenSelect = document.getElementById('kabupaten');
            const kecamatanSelect = document.getElementById('kecamatan');
            const desaSelect = document.getElementById('desa');

            // Reset dropdown yang bergantung
            kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            if (provinceId) {
                fetch(api_main_url + 'kabupaten/' + provinceId, {
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(response => response.json())
                .then(data => {
                    data.kabupaten.forEach(kabupaten => {
                        const option = document.createElement('option');
                        option.value = kabupaten.id;
                        option.textContent = kabupaten.kabupaten;
                        kabupatenSelect.appendChild(option);
                    });
                    // Set kabupaten yang sesuai dengan data yang diedit
                    kabupatenSelect.value = {!! $data_ruas_jalan->desa->kecamatan->kabupaten_id ?? 'null' !!};
                    // Trigger change event untuk memuat kecamatan
                    kabupatenSelect.dispatchEvent(new Event('change'));
                })
                .catch(error => {
                    console.error('Error fetching kabupaten:', error);
                });
            }
        });

        // Event listener untuk dropdown kabupaten
        document.getElementById('kabupaten').addEventListener('change', function() {
            const kabupatenId = this.value;
            const kecamatanSelect = document.getElementById('kecamatan');
            const desaSelect = document.getElementById('desa');

            // Reset dropdown yang bergantung
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            if (kabupatenId) {
                fetch(api_main_url + 'kecamatan/' + kabupatenId, {
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(response => response.json())
                .then(data => {
                    data.kecamatan.forEach(kecamatan => {
                        const option = document.createElement('option');
                        option.value = kecamatan.id;
                        option.textContent = kecamatan.kecamatan;
                        kecamatanSelect.appendChild(option);
                    });
                    // Set kecamatan yang sesuai dengan data yang diedit
                    kecamatanSelect.value = {!! $data_ruas_jalan->ruasjalan->desa->kecamatan_id ?? 'null' !!};
                    // Trigger change event untuk memuat desa
                    kecamatanSelect.dispatchEvent(new Event('change'));
                })
                .catch(error => {
                    console.error('Error fetching kecamatan:', error);
                });
            }
        });


        // Event listener untuk dropdown kecamatan
        document.getElementById('kecamatan').addEventListener('change', function() {
            const kecamatanId = this.value;
            const desaSelect = document.getElementById('desa');

            // Reset dropdown desa
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            if (kecamatanId) {
                fetch(api_main_url + 'desa/' + kecamatanId, {
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(response => response.json())
                .then(data => {
                    data.desa.forEach(desa => {
                        const option = document.createElement('option');
                        option.value = desa.id;
                        option.textContent = desa.desa;
                        desaSelect.appendChild(option);
                    });
                    // Set desa yang sesuai dengan data yang diedit
                    desaSelect.value = {!! $data_ruas_jalan->ruasjalan->desa_id ?? 'null' !!};
                })
                .catch(error => {
                    console.error('Error fetching desa:', error);
                });
            }
        });

    });

    function calculateLength(latlngs) {
        let length = 0;
        for (let i = 0; i < latlngs.length - 1; i++) {
            length += latlngs[i].distanceTo(latlngs[i + 1]);
        }
        return length;
    }

    // Fungsi untuk menangani pengiriman formulir
    document.getElementById('form').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const token = document.querySelector('meta[name="api-token"]').getAttribute('content');

        // Mendapatkan koordinat dari polyline yang digambar
        const latlngs = drawnItems.getLayers()[0].getLatLngs();
        const length = calculateLength(latlngs);
        formData.set('panjang', length.toFixed(2)); // Tambahkan panjang ke formData

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Authorization': 'Bearer ' + token,
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Data berhasil diperbarui!');
                window.location.href = '{{ route("polyline.index") }}';
            } else {
                alert('Gagal memperbarui data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memperbarui data.');
        });
    });

</script>
@endpush

