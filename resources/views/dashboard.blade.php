@extends('layouts.app')
@extends('layouts.header')

<style>
    #tutorialModal {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999; /* Ensure it's above the map */
}

#tutorialModal .modal-content {
    background-color: rgb(107, 107, 107);
    padding: 2rem;
    border-radius: 0.5rem;
    max-width: 100%;
    max-height: 80vh;
    overflow-y: auto;
}
.hidden {
    display: none !important;
}

</style>
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
                    <a href="{{ route('home') }}" class="btn btn-primary mr-2">Ayo Berjalan</a>
                    <button onclick="showTutorial()" class="btn btn-secondary">Tutorial</button>
                </div>
            </div>
        </div>

        <div class="hero-content text-center text-neutral-content">
        </div>
    </div>
</div>

<!-- Modal Tutorial -->
<div id="tutorialModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-base-100 rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full max-h-[80vh] overflow-y-auto">
        <div class="p-6">
            <h3 class="text-2xl font-bold text-base-content mb-4">Tutorial Penggunaan Jalanan</h3>
            <p justify-center>
                <strong class="text-primary">Selamat Datang:</strong> Anda berada di halaman utama Jalanan, platform untuk mengetahui jalan-jalan di Indonesia.
            </p>
            <div class="mt-4 text-left">
                <ol class="list-decimal pl-5 space-y-3 text-base-content">
                    
                    <li class="hover:bg-base-200 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Memulai Eksplorasi:</strong> Klik tombol "Ayo Berjalan" untuk membuka peta interaktif dan mulai menjelajahi jalan-jalan.
                    </li>
                    <li class="hover:bg-base-200 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Tutorial Lengkap:</strong> Klik tombol "Tutorial" untuk melihat panduan lengkap penggunaan fitur-fitur di halaman peta.
                    </li>
                    <li class="hover:bg-base-200 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Navigasi Peta:</strong> Di halaman peta, gunakan mouse untuk menggeser dan zoom peta.
                    </li>
                    <li class="hover:bg-base-200 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Pencarian dan Filter:</strong> Gunakan fitur pencarian dan filter untuk menemukan jalan spesifik atau menyaring berdasarkan kriteria tertentu.
                    </li>
                    <li class="hover:bg-base-200 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Informasi Jalan:</strong> Klik pada garis jalan di peta untuk melihat informasi detail tentang ruas jalan tersebut.
                    </li>
                    <li class="hover:bg-base-200 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Manajemen Data:</strong> Gunakan tombol aksi pada popup informasi jalan untuk mengelola data jalan (detail, edit, hapus).
                    </li>
                    <li class="hover:bg-base-200 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Tambah Jalan Baru:</strong> Di halaman peta, gunakan sidebar untuk menambahkan ruas jalan baru ke database.
                    </li>
                    <li class="hover:bg-base-200 p-2 rounded-md transition duration-300">
                        <strong class="text-primary">Bantuan:</strong> Jika Anda memerlukan bantuan lebih lanjut, lihat panduan lengkap atau hubungi tim support kami.
                    </li>
                </ol>
            </div>
            <div class="mt-6">
                <button id="closeTutorial" class="btn btn-primary w-full">
                    Tutup Tutorial
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    function showTutorial() {
        document.getElementById('tutorialModal').classList.remove('hidden');
    }

    document.getElementById('closeTutorial').addEventListener('click', function() {
        document.getElementById('tutorialModal').classList.add('hidden');
    });
</script>

@endsection