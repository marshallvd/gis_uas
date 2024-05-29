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
                    <th>Name</th>
                    <th>Coordinates</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dummy Data -->
                <tr>
                    <td>Route 1</td>
                    <td>[35.6895, 139.6917], [34.0522, -118.2437], [40.7128, -74.0060]</td>
                    <td class="flex space-x-2">
                        <a href="{{ route('polyline.edit') }}" class="btn btn-primary">Edit</a>
                        <form action="#" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td>Route 2</td>
                    <td>[48.8566, 2.3522], [51.5074, -0.1278], [52.5200, 13.4050]</td>
                    <td class="flex space-x-2">
                        <a href="{{ route('polyline.create') }}" class="btn btn-primary">Edit</a>
                        <form action="#" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td>Route 3</td>
                    <td>[37.7749, -122.4194], [47.6062, -122.3321], [45.5152, -122.6784]</td>
                    <td class="flex space-x-2">
                        <a href="#" class="btn btn-primary">Edit</a>
                        <form action="#" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('javascript')
    <script>
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
    </script>
@endpush
