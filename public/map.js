// Initialisation de la map
let latitude=47.218371;
let longitude=-1.553621;
let map = L.map('map').setView([latitude,longitude],13);

// Initialisation du marqueur
let marker = L.marker([latitude, longitude]).addTo(map);

// Gérer les options de la map
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 20,
    minZoom:1,
    attribution: '© OpenStreetMap'
}).addTo(map);

// Gérer le clic sur la carte
map.on('click', function(e) {
    // Obtenez les coordonnées du clic
    let latitude = e.latlng.lat;
    let longitude = e.latlng.lng;

    // Supprimez l'ancien marqueur
    map.removeLayer(marker);

    // Créez un nouveau marqueur à la position du clic
    marker = L.marker([latitude, longitude]).addTo(map);

    marker.bindPopup("<b>Lieu sélectionné</b>").openPopup();

    // Mettre à jour la latitude et la longitude dans le formulaire
    let latitudeText = document.getElementById("lieu_latitude")
    let longitudeText = document.getElementById("lieu_longitude")
    latitudeText.value=latitude;
    longitudeText.value=longitude;
});



// Gérer la recherche de l'adresse
    let resultats = document.getElementById("resultats");

function rechercheAdresse(){
    let adresse = document.getElementById("rechercheAdresse").value;

    let url = 'https://nominatim.openstreetmap.org/search?format=json&limit=5&q=' + adresse;
    fetch(url)
        .then(response=>response.json())
        .then(data => tabAdresses=data)
        .then(show => console.log(tabAdresses))
        .then(show => listerAdresses())
        .catch(err=>console.log(err))
}

function listerAdresses(){
    resultats.innerHTML=''
    if (tabAdresses.length>0) {
        tabAdresses.forEach(element => {
            resultats.innerHTML+=
                "<button class = 'resultats' type='button' onclick='appliquerAdresse("+JSON.stringify(element)+ ")'>"
                + element.display_name
                +"</button>"
                +"<br>"
                +"<br>";
        });
    } else {
        resultats.innerHTML = "<p style ='color: red'>Not Found</p>"
    }
}


function appliquerAdresse(element) {

    // Supprimez l'ancien marqueur
    map.removeLayer(marker);

    // Créez un nouveau marqueur à partir de l'adresse donnée
    marker = L.marker([element.lat, element.lon]).addTo(map);

    // Mettre à jour la latitude et la longitude dans le formulaire
    let latitudeText = document.getElementById("lieu_latitude")
    let longitudeText = document.getElementById("lieu_longitude")
    latitudeText.value=element.lat;
    longitudeText.value=element.lon;

    // Mettre à jour la map
    map.flyTo([element.lat, element.lon]);
}




