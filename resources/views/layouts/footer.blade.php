<body class="flex flex-col min-h-screen">

    <!-- Main content wrapper -->
    <div class="flex-grow">
        <!-- Your main content goes here -->
        <div class="container mx-auto p-5">
            <!-- Content -->
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 rounded shadow-lg p-5 mt-4 text-white">
        <div class="container mx-auto">
            <div class="flex flex-wrap justify-center items-center">
                <div class="w-full md:w-1/2 mb-3 md:mb-0 text-center">
                    <!-- Social Media Links -->
                    <div class="flex justify-center space-x-4">
                        <a href="https://www.linkedin.com/in/marshallvd/" target="_blank">
                            <img class="avatar rounded-full" src="{{ asset('storage/logo/linkedin.png') }}" alt="LinkedIn Logo" width="30" height="30">
                        </a>
                        <a href="https://www.instagram.com/marshallvd/?hl=en" target="_blank">
                            <img class="avatar rounded-full" src="{{ asset('storage/logo/instagram.png') }}" alt="Instagram Logo" width="30" height="30">
                        </a>
                    </div>
                    <div class="mt-3">
                        <!-- NIM -->
                        <p class="mb-0 text-primary"><b>2105551093</b></p>
                    </div>
                </div>
                <div class="w-full md:w-1/2 text-center">
                    <!-- Tahun dan Hak Cipta -->
                    <p class="mb-0">© 2024 - <span class="current-year"></span> Sistem Informasi Geografis</p>
                </div>
            </div>
        </div>
    </footer>

</body>

</main>

<!-- Core -->

<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

@stack('javascript')
<!-- Volt JS -->
{{-- <script src="{{ asset('volt/hmtl&css/assets/js/volt.js') }}"></script> --}}
<script>
    window.setTimeout(function(){
        $("alert").fadeTo(500,0).slideUp(500,function(){
            $(this).remove()
        })
    },3000);
</script>
