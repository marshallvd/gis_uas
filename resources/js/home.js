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
