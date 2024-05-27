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
                    <div class="dropdown dropdown-end">
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
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" >Logout</button>
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
</body>
</html>
