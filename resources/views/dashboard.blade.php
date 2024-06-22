@extends('layouts.app')
@extends('layouts.header')


@section('contents')
<div>
    <div class="hero min-h-screen" style="background-image: url(https://daisyui.com/images/stock/photo-1507358522600-9f71e620c44e.jpg);">
        <div class="hero-overlay bg-opacity-60"></div> 

    <div class="hero min-h-screen bg-base-200">
        <div class="hero-content flex-col lg:flex-row">
            <img src="https://img.daisyui.com/images/stock/photo-1635805737707-575885ab0820.jpg" class="max-w-sm rounded-lg shadow-2xl" />
            <div class="max-w-md">
                <h1 class="mb-5 text-5xl font-bold">Welcome to Jalanan</h1>
                <p class="mb-5">Jalanan adalah sebuah platform website yang bisa membantu kalian untuk mengetahui jalan-jalan di Indonesia</p>
                <br>
                <a href="{{ route('home') }}" class="btn btn-primary">Ayo Berjalan</a>
            </div>
        </div>
        </div>

        <div class="hero-content text-center text-neutral-content">

        </div>
    </div>
    
</div>


@endsection

