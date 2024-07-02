<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Dashboard</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container mx-auto px-4">
        <div class="navbar bg-base-100">
            <div class="flex-1">
                <a href="{{ route('dashboard') }}" class="btn btn-ghost text-xl">Jalanan</a>
            </div>
            <div class="flex-none">
                @if (session('token'))
                    <div class="dropdown dropdown-end" id="profileDropdown">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                            <div class="w-10 rounded-full">
                                <img src="{{ asset('storage/logo/logo.jpeg') }}" alt="Logo" style="width: 100px; height: auto;">
                            </div>
                        </div>
                        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                            <li>
                                <a href="{{ route('profile') }}" class="justify-between">
                                    Profile
                                </a>
                            </li>
                            <li><a>Settings</a></li>
                            <li>
                                <button type="button" onclick="confirmLogout()">Logout</button>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-xs sm:btn-sm md:btn-md lg:btn-lg">Login</a>
                @endif
            </div>
        </div>

        <div>
            <div>@yield('contents')</div>
        </div>

        @extends('layouts.footer')
    </div>
    @stack('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            checkAuthStatus();
        });

        function checkAuthStatus() {
            const token = sessionStorage.getItem('token');
            const authButtons = document.getElementById('authButtons');
            
            if (token) {
                authButtons.innerHTML = `
                    <div class="dropdown dropdown-end" id="profileDropdown">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                            <div class="w-10 rounded-full">
                                <img src="{{ asset('storage/logo/logo.jpeg') }}" alt="Logo" style="width: 100px; height: auto;">
                            </div>
                        </div>
                        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                            <li><a href="{{ route('profile') }}">Profile</a></li>
                            <li><a>Settings</a></li>
                            <li><button onclick="confirmLogout()">Logout</button></li>
                        </ul>
                    </div>
                `;
            } else {
                authButtons.innerHTML = `<a href="{{ route('login') }}" class="btn btn-xs sm:btn-sm md:btn-md lg:btn-lg">Login</a>`;
            }
        }

        function confirmLogout() {
            Swal.fire({
                title: 'Yakin mau log out?',
                text: "Kalau sudah log out gabisa balik lagi loh !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, logout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    logout();
                }
            });
        }

        function logout() {
    const token = sessionStorage.getItem('token');
    fetch('{{ route('logout') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Authorization': `Bearer ${token}`
        },
        credentials: 'same-origin'
    }).then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            sessionStorage.removeItem('token');
            Swal.fire({
                icon: 'success',
                title: 'Logout Berhasil',
                text: data.message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            }).then(() => {
                checkAuthStatus();
                window.location.href = '{{ route('dashboard') }}';
            });
        } else {
            throw new Error(data.message || 'Logout gagal');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: 'Gagal melakukan logout. Silakan coba lagi.',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    });
}
    </script>
</body>
</html>