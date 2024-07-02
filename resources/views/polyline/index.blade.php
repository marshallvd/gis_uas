@extends('layouts.app')
@extends('layouts.header')

@section('css')
    <!-- Menambahkan stylesheet Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <canvas id="kondisiChart"></canvas>
        </div>
    </div>

    <!-- Jenis Jalan -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-secondary">Jenis Jalan</h2>
            <canvas id="jenisChart"></canvas>
        </div>
    </div>

    <!-- Total Panjang Jalan -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-accent">Panjang Jalan per Kondisi</h2>
            <canvas id="panjangChart"></canvas>
        </div>
    </div>
</div>


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

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-accent">Total Jalan</h2>
            <div class="stat">
                <div class="stat-title">Jumlah Ruas</div>
                <div class="stat-value">0</div>
                <div class="stat-desc">ruas</div>
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

{{-- <script>

const totalJalan = 0;
            ruasJalan.forEach(ruaS=>{
                totalJalan++;

            })
console.log('Total Jalan :' totalJalan);
</script> --}}
<script>
    localStorage.setItem("token", "{{ session('token') }}");
    localStorage.setItem("api_main_url", "https://gisapis.manpits.xyz/api/");
</script>
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-geometryutil@0.0.2/dist/leaflet.geometryutil.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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

let fetchRuasJalanCalled = false;

async function initializeData() {
    if (!fetchRuasJalanCalled) {
        fetchRuasJalanCalled = true;
        await fetchRuasJalan();
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    const token = localStorage.getItem("token");
    const api_main_url = localStorage.getItem("api_main_url");
    const kondisiLabels = {
        1: 'Baik',
        2: 'Sedang',
        3: 'Rusak'
    };

    const jenisJalanLabels = {
        1: 'Desa',
        2: 'Kabupaten',
        3: 'Provinsi'
    };

    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');

    searchInput.addEventListener('input', handleSearchAndSort);
    sortSelect.addEventListener('change', handleSearchAndSort);

    if (!token || !api_main_url) {
        console.error('Token or API URL is missing');
        return;
    }

    const headers = {
        "Authorization": `Bearer ${token}`,
        "Content-Type": "application/json"
    };

    let charts = {};
    let globalRuasJalanData;
    let globalEksistingMap;
    let globalJenisJalanMap;
    let globalKondisiMap;

    function createChart(id, type, data, title) {
        const ctx = document.getElementById(id).getContext('2d');
        
        if (charts[id]) {
            charts[id].destroy();
        }
        
        let labels, values;
        if (id === 'kondisiChart' || id === 'panjangChart') {
            labels = ['Baik', 'Sedang', 'Rusak'];
            values = [data[1] || 0, data[2] || 0, data[3] || 0];
        } else if (id === 'jenisChart') {
            labels = ['Desa', 'Kabupaten', 'Provinsi'];
            values = [data[1] || 0, data[2] || 0, data[3] || 0];
        }

        charts[id] = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: title,
                    data: values,
                    backgroundColor: ['#4BC0C0', '#FFCE56', '#FF6384'],
                    borderColor: '#36A2EB',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: title
                    }
                },
                scales: type === 'pie' ? {} : {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    async function fetchData(endpoint) {
        try {
            const response = await axios.get(api_main_url + endpoint, { headers });
            console.log(`Response for ${endpoint}:`, response.data);
            return response.data;
        } catch (error) {
            console.error(`Error fetching ${endpoint} data:`, error);
            return null;
        }
    }

    function groupById(data, key) {
        return data.reduce((result, item) => {
            const groupKey = item[key];
            if (groupKey !== undefined && groupKey !== null) {
                result[groupKey] = (result[groupKey] || 0) + 1;
            }
            return result;
        }, {});
    }

    function groupByIdAndSum(data, key, sumKey) {
        return data.reduce((result, item) => {
            const groupKey = item[key];
            const value = parseFloat(item[sumKey]);
            if (groupKey !== undefined && groupKey !== null && !isNaN(value)) {
                result[groupKey] = (result[groupKey] || 0) + value;
            }
            return result;
        }, {});
    }

    function updateInsight(kondisiCounts, jenisCounts, totalPanjang, totalJalan) {
        document.querySelector('.stat-value.text-success').textContent = (kondisiCounts[1] || 0).toString();
        document.querySelector('.stat-value.text-warning').textContent = (kondisiCounts[2] || 0).toString();
        document.querySelector('.stat-value.text-error').textContent = (kondisiCounts[3] || 0).toString();

        const statsElements = document.querySelectorAll('.card:nth-child(2) .card-body .stats .stat');
        if (statsElements.length >= 3) {
            statsElements[0].querySelector('.stat-value').textContent = (jenisCounts[3] || 0).toString();
            statsElements[1].querySelector('.stat-value').textContent = (jenisCounts[2] || 0).toString();
            statsElements[2].querySelector('.stat-value').textContent = (jenisCounts[1] || 0).toString();
        }

        const totalPanjangElement = document.querySelector('.card:nth-child(3) .card-body .stat .stat-value');
        if (totalPanjangElement) {
            totalPanjangElement.textContent = totalPanjang.toFixed(2);
        }


        const totalJalanElement = document.querySelector('.card:nth-child(4) .card-body .stat .stat-value');
        if (totalJalanElement) {
            totalJalanElement.textContent = totalJalan.toString();
        }
    }

    async function fetchRuasJalan() {
        try {
            const [ruasJalanData, eksistingData, jenisJalanData, kondisiData] = await Promise.all([
                fetchData("ruasjalan"),
                fetchData("meksisting"),
                fetchData("mjenisjalan"),
                fetchData("mkondisi")
            ]);

            console.log('All fetched data:', { ruasJalanData, eksistingData, jenisJalanData, kondisiData });

            if (!ruasJalanData || !Array.isArray(ruasJalanData.ruasjalan)) {
                throw new Error('Invalid or missing ruas jalan data');
            }

            const ruasJalan = ruasJalanData.ruasjalan;
            globalEksistingMap = new Map(eksistingData?.eksisting?.map(item => [item.id, item.eksisting]) || []);
            globalKondisiMap = new Map(kondisiData?.kondisi?.map(item => [item.id, item.kondisi]) || []);
            globalJenisJalanMap = new Map(jenisJalanData?.jenisjalan?.map(item => [item.id, item.jenisjalan]) || []);
            
            console.log('Processed maps:', { globalEksistingMap, globalJenisJalanMap, globalKondisiMap });

            const kondisiCounts = groupById(ruasJalan, 'kondisi_id');
            const jenisCounts = groupById(ruasJalan, 'jenisjalan_id');
            const panjangPerKondisi = groupByIdAndSum(ruasJalan, 'kondisi_id', 'panjang');

            console.log('Grouped data:', { kondisiCounts, jenisCounts, panjangPerKondisi });

            const totalPanjang = ruasJalan.reduce((total, item) => total + (parseFloat(item.panjang || 0) / 1000), 0);
            const totalJalan = ruasJalan.length;
            

            updateInsight(kondisiCounts, jenisCounts, totalPanjang, totalJalan);

            createChart('kondisiChart', 'pie', kondisiCounts, 'Distribusi Kondisi Jalan');
            createChart('jenisChart', 'bar', jenisCounts, 'Jumlah Jalan per Jenis');
            createChart('panjangChart', 'line', panjangPerKondisi, 'Panjang Jalan per Kondisi');

            globalRuasJalanData = ruasJalan;
            handleSearchAndSort();

        } catch (error) {
            console.error('Error fetching data:', error);
            Swal.fire('Error!', 'Gagal mengambil data ruas jalan. ' + error.message, 'error');
        }
    }



    function handleSearchAndSort() {
        if (!globalRuasJalanData) return;

        const searchValue = searchInput.value.toLowerCase();
        const sortBy = sortSelect.value;

        let filteredData = globalRuasJalanData.filter(polyline => 
            polyline.nama_ruas.toLowerCase().includes(searchValue)
        );

        if (sortBy) {
            filteredData.sort((a, b) => {
                if (a[sortBy] < b[sortBy]) return -1;
                if (a[sortBy] > b[sortBy]) return 1;
                return 0;
            });
        }

        updateTable(filteredData);
    }


    function deletePolyline(id) {
        Swal.fire({
            title: 'Yakin mau dihapus?',
            text: "Kalau dihapu tidak bisa balik lagi loh!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`${api_main_url}ruasjalan/${id}`, {
                    headers: {
                        "Authorization": `Bearer ${localStorage.getItem("token")}`,
                        "Content-Type": "application/json"
                    }
                })
                .then(response => {
                    Swal.fire(
                        'Terhapus!',
                        'Data berhasil dihapus.',
                        'success'
                    );
                    // Refresh data or remove row from table
                    fetchRuasJalan();
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'Data Gagal dihapus.',
                        'error'
                    );
                });
            }
        });
    }

    // Add this function to handle click events on delete buttons
    function handleDeleteClick(e) {
        if (e.target && e.target.classList.contains('btn-delete')) {
            e.preventDefault();
            const id = e.target.getAttribute('data-id');
            deletePolyline(id);
        }
    }


    function updateTable(filteredData) {
        const tableBody = document.getElementById('polylineTableBody');
        tableBody.innerHTML = '';

        filteredData.forEach((polyline, index) => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-100';
            row.innerHTML = `
                <td class="p-2 text-center">${index + 1}</td>
                <td class="p-2 text-center">${polyline.nama_ruas}</td>
                <td class="p-2 text-center truncate" title="${polyline.paths}">${polyline.paths.substring(0, 30)}...</td>
                <td class="p-2 text-center">${polyline.panjang}</td>
                <td class="p-2 text-center">${polyline.lebar}</td>
                <td class="p-2 text-center">${globalEksistingMap.get(polyline.eksisting_id) || 'N/A'}</td>
                <td class="p-2 text-center">${globalKondisiMap.get(polyline.kondisi_id) || kondisiLabels[polyline.kondisi_id] || 'N/A'}</td>
                <td class="p-2 text-center">${globalJenisJalanMap.get(polyline.jenisjalan_id) || jenisJalanLabels[polyline.jenisjalan_id] || 'N/A'}</td>
                <td class="p-2 text-center">${polyline.keterangan}</td>
                <td class="p-2 text-center flex justify-center space-x-2">
                    <a href="/polyline/${polyline.id}" class="btn btn-warning btn-xs">Detail</a>

                    <a href="/polyline/${polyline.id}/edit" class="btn btn-accent btn-xs">Edit</a>

                    <button type="button" class="btn btn-error btn-xs btn-delete" data-id="{{ $polyline['id'] }}">Delete</button>
                </td>
            `;
            tableBody.appendChild(row);

            
        });
        const deleteButtons = tableBody.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', handleDeleteClick);
        });
    }

    await fetchRuasJalan();



});
</script>
@endpush