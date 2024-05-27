<!-- resources/views/polyline/index.blade.php -->
@extends('layouts.app')

@section('contents')
<div class="container">
    <h1>Data Polyline</h1>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tambahkan tombol "Create Data" di atas kolom aksi -->
    <div class="d-flex justify-content-end mb-3 ">
        <a href="{{ route('polyline.create') }}" class="btn btn-outline btn-primary">Create Data</a>
    </div>
    {{-- float-end --}}

    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Coordinates</th>
                    <th>Action</th> <!-- Tambah kolom untuk aksi -->
                </tr>
            </thead>
            <tbody>
                @foreach ($polylines as $polyline)
                    <tr>
                        <td>{{ $polyline->name }}</td>
                        <td>{{ implode(', ', json_decode($polyline->coordinates, true)) }}</td>
                        <td>
                            <!-- Tambahkan tombol edit dan hapus -->
                            <a href="{{ route('polyline.edit', $polyline->id) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('polyline.destroy', $polyline->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('javascript')
    <script>
        $('#delete').on('click', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            Swal.fire({
                title: 'Apakah kamu yakin ?',
                text: "Data terhapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Delete!'
            }).then((result) => {
                if (result.isConfirmed) {

                    document.getElementById('deleteForm').action = href
                    document.getElementById('deleteForm').submit()
                    
                    Swal.fire(
                        'Deleted!',
                        'Data telah terhapus!.',
                        'success'
                    )
                }
            })
        })
    </script>
@endpush