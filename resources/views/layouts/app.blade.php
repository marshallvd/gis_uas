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
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container mx-auto px-4">
        <div class="navbar bg-base-100">
            <div class="flex-1">
                <a href="{{ route('dashboard') }}" class="btn btn-ghost text-xl">Jalanan</a>
            </div>
            <div class="flex-none">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" class="mt-3 z-[1] card card-compact dropdown-content w-52 bg-base-100 shadow">
                    </div>
                </div>
                @if (Auth::check())
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
                                <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                                    @csrf
                                    <button type="button" onclick="confirmLogout(event)">Logout</button>
                                    @if(session('status'))
                                        <div class="alert alert-success">
                                            {{ session('status') }}
                                        </div>
                                    @endif
                                </form>
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
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            @if(session('status'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('status') }}',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif

            const profileDropdown = document.getElementById('profileDropdown');

            if (profileDropdown) {
                profileDropdown.addEventListener('click', () => {
                    // Toggle visibility of dropdown menu
                    const dropdownMenu = profileDropdown.querySelector('.dropdown-content');
                    dropdownMenu.classList.toggle('hidden');
                });
            }
        });

        function confirmLogout(event) {
            event.preventDefault();
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
                    localStorage.removeItem('token');
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        function logout(event) {
            event.preventDefault();
            localStorage.removeItem('token');
            if (event.target.closest('form').id === 'logoutForm') {
                document.getElementById('logoutForm').submit();
            } else {
                document.getElementById('logoutFormSidebar').submit();
            }
        }
    </script>
</body>
</html>
