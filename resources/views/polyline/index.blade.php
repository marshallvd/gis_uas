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

        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a {
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #ddd;
            color: #007bff;
            border-radius: 3px;
        }

        .pagination li.active a {
            background-color: #007bff;
            color: white;
        }

        .overflow-y-scroll {
            max-height: 500px; /* Atur ketinggian maksimum sesuai kebutuhan */
            overflow-y: auto;
        }

    </style>
@endsection

@section('contents')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-3">
        <h1 class="text-3xl font-bold">Data Jalan Provinsi Bali</h1>
        <div>
            <a href="{{ route('home') }}" class="btn btn-outline btn-accent">Dashboard</a>
            <a href="{{route('polyline.create', ['previous' => 'index']) }}" class="btn btn-outline btn-primary">Create Data</a>
        </div>

    </div>


    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
<!-- Insight Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <!-- Kondisi Jalan -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-primary">Kondisi Jalan</h2>
            <div class="stats stats-vertical shadow">
                <div class="stat">
                    <div class="stat-title">Baik</div>
                    <div class="stat-value text-success">0</div>
                </div>
                <div class="stat">
                    <div class="stat-title">Sedang</div>
                    <div class="stat-value text-warning">0</div>
                </div>
                <div class="stat">
                    <div class="stat-title">Rusak</div>
                    <div class="stat-value text-error">0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jenis Jalan -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-secondary">Jenis Jalan</h2>
            <div class="stats stats-vertical shadow">
                <div class="stat">
                    <div class="stat-title">Jalan Provinsi</div>
                    <div class="stat-value">0</div>
                </div>
                <div class="stat">
                    <div class="stat-title">Jalan Kabupaten</div>
                    <div class="stat-value">0</div>
                </div>
                <div class="stat">
                    <div class="stat-title">Jalan Desa</div>
                    <div class="stat-value">0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Panjang Jalan -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-accent">Total Panjang Jalan</h2>
            <div class="stat">
                <div class="stat-title">Panjang (km)</div>
                <div class="stat-value">0</div>
                <div class="stat-desc">km</div>
            </div>
        </div>
    </div>
</div>
<div class="mb-4 flex justify-between">
    <input type="text" id="searchInput" class="input input-bordered w-full max-w-xs" placeholder="Search by Nama Ruas...">
    <select id="sortSelect" class="select select-bordered w-full max-w-xs">
        <option value="">Sort by</option>
        <option value="nama_ruas">Nama Ruas</option>
        <option value="panjang">Panjang</option>
        <option value="lebar">Lebar</option>
    </select>
</div>

    <div class="overflow-x-auto overflow-y-scroll">
        {{-- <div id="map" class="w-full h-96 mb-4"></div> --}}
        <table class="table w-full table-auto">
            <thead class="bg-gray-700 text-white">
                <tr>
                    <th class="p-2 text-center">No</th>
                    <th class="p-2 text-center">Nama Ruas</th>
                    <th class="p-2 text-center">Koordinat</th>
                    <th class="p-2 text-center">Panjang (m)</th>
                    <th class="p-2 text-center">Lebar (m)</th>
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
                        <td class="p-2 text-center"></td>
                        <td class="p-2 text-center">{{ $polyline['nama_ruas'] }}</td>
                        <td class="p-2 text-center truncate" title="{{ $polyline['paths'] }}">{{ Str::limit($polyline['paths'], 30) }}</td>
                        <td class="p-2 text-center">{{ ($polyline['panjang'] )}}</td>
                        <td class="p-2 text-center">{{ $polyline['lebar'] }}</td>
                        <td class="p-2 text-center">{{ $polyline['eksisting_id'] }}</td>
                        <td class="p-2 text-center">{{ $polyline['kondisi_id'] }}</td>
                        <td class="p-2 text-center">{{ $polyline['jenisjalan_id'] }}</td>
                        <td class="p-2 text-center">{{ $polyline['keterangan'] }}</td>
                        <td class="p-2 text-center flex justify-center space-x-2">
                            <form action="{{ route('polyline.detail', $polyline['id']) }}" method="GET" >
                                @csrf
                                @method('GET')
                                <a href="{{ route('polyline.detail', $polyline['id']) }}" class="btn btn-warning btn-xs">Detail</a>
                            </form>
                            <form action="{{ route('polyline.edit', $polyline['id']) }}" method="GET" >
                                @csrf
                                @method('GET')
                                <a href="{{ route('polyline.edit', ['id' => $polyline['id'], 'previous' => 'index'])}}" class="btn btn-accent btn-xs">Edit</a>
                            </form>
                            

                            <form action="{{ route('polyline.destroy', $polyline['id']) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button-delete" class=" btn btn-error btn-xs btn-delete" data-id="{{ $polyline['id'] }}">Delete</button>
                            </form>   
                            

                            
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    {{-- <div class="mt-4">
        <ul class="pagination">
            <!-- Paginasi akan diisi oleh JavaScript -->
        </ul>
    </div> --}}
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
    $(document).ready(function() {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>
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

    let map;
    // Kode inisialisasi map (tidak diubah)

    function parseCoordinates(coords) {
        // Kode tidak diubah
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

    // Event listener for delete buttons
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('btn-delete')) {
            const id = event.target.getAttribute('data-id');
            Swal.fire({
                title: 'Kamu yakin mau hapus?',
                text: 'Kalau sudah dihapus tidak bida kembali loh!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Eitt Jangan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    handleDelete(id);
                }
            });
        }
    });

    function groupById(data, key) {
        console.log(`Grouping by ${key}`);
        console.log("Input data:", data);

        const result = data.reduce((result, item) => {
            const groupKey = item[key];
            if (!result[groupKey]) {
                result[groupKey] = 0;
            }
            result[groupKey]++;
            console.log(`Incrementing ${groupKey}. New total: ${result[groupKey]}`);
            return result;
        }, {});

        console.log("Grouped result:", result);
        return result;
    }

    async function fetchRuasJalan() {
        try {
            console.log("Fetching ruas jalan data...");
            const response = await axios.get(api_main_url + "ruasjalan", { headers });
            console.log("Raw API Response:", response.data);

            if (response.status !== 200 || response.data.code !== 200) {
                throw new Error('Failed to fetch ruas jalan data: ' + response.statusText);
            }

            const ruasJalanData = response.data.ruasjalan;

            if (ruasJalanData && Array.isArray(ruasJalanData) && ruasJalanData.length > 0) {
                console.log('Total ruas jalan:', ruasJalanData.length);
                console.log('Sample data (first 3 items):', ruasJalanData.slice(0, 3));

                // Mengelompokkan dan menghitung panjang berdasarkan kondisi (dalam km)
                const kondisiCounts = groupById(ruasJalanData, 'kondisi_id', 'panjang');
                console.log("Kondisi counts:", kondisiCounts);

                const successElement = document.querySelector('.stat-value.text-success');
                const warningElement = document.querySelector('.stat-value.text-warning');
                const errorElement = document.querySelector('.stat-value.text-error');

                if (successElement && warningElement && errorElement) {
                    console.log("Before update - Baik:", successElement.textContent);
                    successElement.textContent = (kondisiCounts[1] || 0).toFixed(2);
                    console.log("After update - Baik:", successElement.textContent);

                    console.log("Before update - Sedang:", warningElement.textContent);
                    warningElement.textContent = (kondisiCounts[2] || 0).toFixed(2);
                    console.log("After update - Sedang:", warningElement.textContent);

                    console.log("Before update - Rusak:", errorElement.textContent);
                    errorElement.textContent = (kondisiCounts[3] || 0).toFixed(2);
                    console.log("After update - Rusak:", errorElement.textContent);
                } else {
                    console.error("One or more stat elements not found!");
                }

                // Mengelompokkan dan menghitung panjang berdasarkan jenis jalan (dalam km)
                const jenisCounts = groupById(ruasJalanData, 'jenisjalan_id', 'panjang');
                console.log("Jenis jalan counts:", jenisCounts);

                const statsElements = document.querySelectorAll('.card:nth-child(2) .card-body .stats .stat');
                if (statsElements.length >= 3) {
                    statsElements[0].querySelector('.stat-value').textContent = (jenisCounts[1] || 0).toFixed(2);
                    statsElements[1].querySelector('.stat-value').textContent = (jenisCounts[2] || 0).toFixed(2);
                    statsElements[2].querySelector('.stat-value').textContent = (jenisCounts[3] || 0).toFixed(2);
                } else {
                    console.error("Jenis jalan stat elements not found!");
                }

                // Menghitung total panjang jalan (dalam km)
                const totalPanjang = ruasJalanData.reduce((total, item) => total + (parseFloat(item.panjang || 0) / 1000), 0);
                console.log("Total panjang jalan (km):", totalPanjang);

                const totalPanjangElement = document.querySelector('.card:nth-child(3) .card-body .stat .stat-value');
                if (totalPanjangElement) {
                    console.log("Before update - Total Panjang:", totalPanjangElement.textContent);
                    totalPanjangElement.textContent = totalPanjang.toFixed(2);
                    console.log("After update - Total Panjang:", totalPanjangElement.textContent);
                } else {
                    console.error("Total panjang jalan element not found!");
                }

            } else {
                console.log("Data ruasjalan kosong atau tidak valid.");
            }

            return response.data;
        } catch (error) {
            console.error('Error fetching data:', error);
            if (error.response) {
                console.error('Server responded with:', error.response.status, error.response.data);
            } else if (error.request) {
                console.error('No response received:', error.request);
            } else {
                console.error('Error message:', error.message);
            }
            Swal.fire('Error!', 'Gagal mengambil data ruas jalan.', 'error');
        }
    }




    // Memanggil fungsi fetchRuasJalan
    await fetchRuasJalan();

    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('polylineTableBody');
        const searchInput = document.getElementById('searchInput');
        const sortSelect = document.getElementById('sortSelect');
        const paginationContainer = document.querySelector('.pagination');

        let allData = [];
        const itemsPerPage = 100;
        let currentPage = 1;

        // Ambil semua data dari tabel
        document.querySelectorAll('#polylineTableBody tr').forEach((row, index) => {
            allData.push({
                element: row.cloneNode(true), // Menggunakan cloneNode agar tidak menghapus dari DOM
                nama_ruas: row.children[1].textContent,
                panjang: parseFloat(row.children[3].textContent) || 0,
                lebar: parseFloat(row.children[4].textContent) || 0
            });
        });

        function renderTable(data, page = 1) {
            const start = (page - 1) * itemsPerPage;
            const paginatedData = data.slice(start, start + itemsPerPage);

            tableBody.innerHTML = '';
            paginatedData.forEach((item, index) => {
                const row = item.element.cloneNode(true);
                row.children[0].textContent = start + index + 1; // Update nomor urut
                tableBody.appendChild(row);
            });

            renderPagination(data.length, page);
        }

        function renderPagination(totalItems, currentPage) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            let paginationHTML = '';

            // Tambahkan tombol Previous
            paginationHTML += `<li class="${currentPage === 1 ? 'disabled' : ''}"><a href="#" data-page="${currentPage - 1}">&laquo; Previous</a></li>`;

            // Render maksimal 5 nomor halaman dengan halaman saat ini di tengah jika memungkinkan
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationHTML += `<li class="${i === currentPage ? 'active' : ''}"><a href="#" data-page="${i}">${i}</a></li>`;
            }

            // Tambahkan tombol Next
            paginationHTML += `<li class="${currentPage === totalPages ? 'disabled' : ''}"><a href="#" data-page="${currentPage + 1}">Next &raquo;</a></li>`;

            paginationContainer.innerHTML = paginationHTML;

            paginationContainer.querySelectorAll('a').forEach(a => {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const newPage = parseInt(this.getAttribute('data-page'), 100);
                    if (newPage >= 1 && newPage <= totalPages) {
                        currentPage = newPage;
                        renderTable(allData, currentPage);
                    }
                });
            });
        }

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filteredData = allData.filter(item => 
                item.nama_ruas.toLowerCase().includes(searchTerm)
            );
            currentPage = 1;
            renderTable(filteredData, currentPage);
        });

        sortSelect.addEventListener('change', function() {
            const sortBy = this.value;
            if (sortBy) {
                allData.sort((a, b) => {
                    if (sortBy === 'nama_ruas') {
                        return a[sortBy].localeCompare(b[sortBy]);
                    } else {
                        return a[sortBy] - b[sortBy];
                    }
                });
            }
            currentPage = 1;
            renderTable(allData, currentPage);
        });

        // Inisialisasi tabel
        renderTable(allData, currentPage);

        
    });


    const tableBody = document.getElementById('polylineTableBody');
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const paginationContainer = document.querySelector('.pagination');

    let allData = [];
    const itemsPerPage = 100;
    let currentPage = 1;

    // Ambil semua data dari tabel
    document.querySelectorAll('#polylineTableBody tr').forEach((row, index) => {
        allData.push({
            element: row,
            nama_ruas: row.children[1].textContent,
            panjang: parseFloat(row.children[3].textContent),
            lebar: parseFloat(row.children[4].textContent)
        });
    });

    function renderTable(data, page = 1) {
        const start = (page - 1) * itemsPerPage;
        const paginatedData = data.slice(start, start + itemsPerPage);

        tableBody.innerHTML = '';
        paginatedData.forEach((item, index) => {
            const row = item.element.cloneNode(true);
            row.children[0].textContent = start + index + 1; // Update nomor urut
            tableBody.appendChild(row);
        });

        renderPagination(data.length, page);
    }

    function renderPagination(totalItems, currentPage) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        let paginationHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += `<li class="${i === currentPage ? 'active' : ''}"><a href="#" data-page="${i}">${i}</a></li>`;
        }

        paginationContainer.innerHTML = paginationHTML;

        paginationContainer.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                currentPage = parseInt(this.getAttribute('data-page'), 10);
                renderTable(allData, currentPage);
            });
        });
    }

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filteredData = allData.filter(item => 
            item.nama_ruas.toLowerCase().includes(searchTerm)
        );
        currentPage = 1;
        renderTable(filteredData, currentPage);
    });

    sortSelect.addEventListener('change', function() {
        const sortBy = this.value;
        if (sortBy) {
            allData.sort((a, b) => (a[sortBy] > b[sortBy]) ? 1 : ((b[sortBy] > a[sortBy]) ? -1 : 0));
        }
        currentPage = 1;
        renderTable(allData, currentPage);
    });

    // Inisialisasi tabel
    renderTable(allData, currentPage);
    });
</script>
@endpush