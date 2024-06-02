@extends('layouts.app')

@section('contents')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-3">
        <h1 class="text-2xl font-bold">Data Polyline</h1>
        <a href="{{ route('polyline.create') }}" class="btn btn-outline btn-primary">Create Data</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Nama Ruas</th>
                    <th>Koordinat</th>
                    <th>Panjang</th>
                    <th>Lebar</th>
                    <th>Eksisting</th>
                    <th>Kondisi</th>
                    <th>Jenis Jalan</th>
                    <th>Keterangan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($polylines['ruasjalan']) && is_array($polylines['ruasjalan']))
                    @foreach ($polylines['ruasjalan'] as $polyline)
                    <tr>
                        <td>{{ $polyline['nama_ruas'] }}</td>
                        <td>{{ $polyline['paths'] }}</td>
                        <td>{{ $polyline['panjang'] }}</td>
                        <td>{{ $polyline['lebar'] }}</td>
                        <td>{{ $polyline['eksisting_id'] }}</td>
                        <td>{{ $polyline['kondisi_id'] }}</td>
                        <td>{{ $polyline['jenisjalan_id'] }}</td>
                        <td>{{ $polyline['keterangan'] }}</td>
                        <td class="flex space-x-2">
                            <a href="{{ route('polyline.edit', $polyline['id']) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('polyline.destroy', $polyline['id']) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('javascript')
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

        async function fetchData(url) {
            const response = await fetch(url, { headers });
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        }

        try {
            console.log('Fetching data from API...');
            const data_region = await fetchData(api_main_url + "api/mregion");
            const data_ruas = await fetchData(api_main_url + "api/ruasjalan");
            const eksistingData = await fetchData(api_main_url + "api/meksisting");
            const kondisiData = await fetchData(api_main_url + "api/mkondisi");
            const jenisJalanData = await fetchData(api_main_url + "api/mjenisjalan");

            console.log('Data Region:', data_region);
            console.log('Data Ruas:', data_ruas);

            const tableBody = document.querySelector("table tbody");

            const formatContentRuas = function (ruas, data_region, eksistingData, kondisiData, jenisJalanData) {
            let data_desa = data_region.desa.find(k => k.id == ruas.desa_id);
            const eksisting = eksistingData.eksisting.find(e => e.id == ruas.eksisting_id);
            const kondisi = kondisiData.kondisi.find(k => k.id == ruas.kondisi_id);
            const jenisjalan = jenisJalanData.jenisjalan.find(j => j.id == ruas.jenisjalan_id);

            return `
                <tr>
                    <td>${ruas.nama_ruas}</td>
                    <td>${ruas.paths}</td>
                    <td>${ruas.panjang}</td>
                    <td>${ruas.lebar}</td>
                    <td>${eksisting ? eksisting.eksisting : '-'}</td>
                    <td>${kondisi ? kondisi.kondisi : '-'}</td>
                    <td>${jenisjalan ? jenisjalan.jenisjalan : '-'}</td>
                    <td>${ruas.keterangan}</td>
                    <td class="flex space-x-2">
                        <a href="/polyline/edit/${ruas.id}" class="btn btn-primary">Edit</a>
                        <form action="/polyline/destroy/${ruas.id}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            `;
        };

            if (Array.isArray(data_ruas.ruasjalan)) {
                data_ruas.ruasjalan.forEach(ruas => {
                    if (typeof ruas === 'object' && ruas !== null && 'nama_ruas' in ruas) {
                        console.log('Processing ruas:', ruas);
                        tableBody.innerHTML += formatContentRuas(ruas, data_region, eksistingData, kondisiData, jenisJalanData);
                    } else {
                        console.error('Invalid ruas data:', ruas);
                    }
                });
            } else {
                console.error('Invalid data_ruas.ruasjalan:', data_ruas.ruasjalan);
            }

            document.querySelectorAll('.btn-danger').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var form = this.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                            Swal.fire(
                                'Deleted!',
                                'Your data has been deleted.',
                                'success'
                            );
                        }
                    });
                });
            });

        } catch (error) {
            console.error('Error fetching data:', error);
        }
    });
</script>
@endpush
