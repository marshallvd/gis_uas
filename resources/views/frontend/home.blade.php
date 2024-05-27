@extends('layouts.app')
@extends('layouts.header')
{{-- @extends('layouts.sidebar') --}}

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map { 
            height: 1000px; 
        }
    </style>
@endsection

@section('contents')
<div class="container flex flex-col ">
    <div class="row justify-content-center flex-grow">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header ">
                    <div class="max-w-md">
                        <h2 class="mb-5 text-3xl font-bold">Perhatikan Jalanmu</h2>
                    </div>
                    @auth
                    <div>
                        <a href="{{ route('polyline.index') }}" class="btn btn-primary a">Tambah Data</a>
                    @endauth
                    </div>
                    <br>
                    
                    <br>
                <div class="card-body">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('javascript')
    <script src="{{ asset('js\home.js') }}"></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

    <script>

    document.addEventListener('DOMContentLoaded', () => {
        const token = localStorage.getItem("token");
        console.log(token);
        const url = "https://gisapis.manpits.xyz/api/user";
        axios.get(url, {
            headers: {
                Authorization: `Bearer ${token}`,
            }
    })
    .then(response => {
        const userName = response.data.data.user.name; // Replace this with dynamic data
        document.querySelector('.user-name').textContent = userName;

        
    })
    .catch(error => {
        console.log(error);
    });

    const map = L.map('map').setView([-8.373099488726732, 115.18725551951702], 10);

    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var Esri_World = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    });
    
    

    var Esri_Map = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; National Geographic, Esri, DeLorme, NAVTEQ, UNEP-WCMC, USGS, NASA, ESA, METI, NRCAN, GEBCO, NOAA, iPC',
        maxZoom: 16
    });
    

    var Stadia_Dark = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.{ext}', {
        minZoom: 0,
        maxZoom: 20,
        attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        ext: 'png'
    });
    
    // Definisikan baseLayers
    var baseLayers = {
        "OSM Tiles": tiles,
        "ESRI World Imagery": Esri_World,
        "ESRI Map": Esri_Map,
        "Stadia Dark": Stadia_Dark
    };

    const layerControl = L.control.layers(baseLayers).addTo(map);

    var markersLayer = new L.layerGroup().addTo(map); // Tambahkan ke peta


    var controlSearch = new L.Control.Search({
        position: 'topleft',
        layer: markersLayer,
        zoom: 15,
        markerLocation: true
    });

    map.addControl(controlSearch);

    var iconMarker = L.icon({
        iconUrl :"{{ asset('storage/marker/marker.png') }}",
        iconSize:     [50, 50], // size of the icon
        shadowSize:   [50, 50], // size of the shadow
        
    })
    var marker = L.marker([-8.373099488726732, 115.18725551951702],{
        icon:iconMarker,
        draggable : true
    })
    .bindPopup('Ada apa disini?')
    .addTo(map);

    

     // Membuat popup baru
    var popup = L.popup({ 
        offset: [0, -20],
        minWidth:240,
        maxWidth: 500
    })
        .setLatLng(marker.getLatLng())
        .setContent('Ini adalah marker di Bali!');
    
    // Binding popup ke marker
    marker.bindPopup(popup);

    // Format popup content
    formatContent = function(lat, lng){
        return `
            <div class="wrapper">
                <div class="row">
                    <div class="cell merged" style="text-align:center"><b>Koordinat</b></div>
                </div>
                <div class="row">
                    <div class="col">Latitude</div>
                    <div class="col">${lat}</div>
                </div>
                <div class="row">
                    <div class="col">Longitude</div>
                    <div class="col">${lng}</div>
                </div>
            </div>
        `;
    }
    
    // Menambahkan event listener pada marker
    marker.on('click', function() {
        popup.setLatLng(marker.getLatLng()),
        popup.setContent(formatContent(marker.getLatLng().lat,marker.getLatLng().lng));
    });

    // Menambahkan event listener pada marker
    marker.on('drag', function(event) {
        popup.setLatLng(marker.getLatLng()),
        popup.setContent(formatContent(marker.getLatLng().lat,marker.getLatLng().lng));
        marker.openPopup();
    });

    setTimeout(function () {
        window.dispatchEvent(new Event("resize"));
    }, 500);

    });

    </script>

    

    
@endpush

