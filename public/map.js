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

function escapeHTML(string) {
    return string.replace(/"/g, '').replace(/'/g, '');
}

function listerAdresses(){
    resultats.innerHTML=''
    if (tabAdresses.length>0) {
        tabAdresses.forEach(element => {
            const displayName = escapeHTML(JSON.stringify(element.display_name));
            const elementLat = JSON.stringify(element.lat);
            const elementLon = JSON.stringify(element.lon);
            resultats.innerHTML+=
                "<button class='resultats' type='button' onclick='appliquerAdresse(\"" + displayName + "\", " + elementLat + ", " + elementLon + ")'>"
                + element.display_name
                +"</button>"
                +"<br>"
                +"<br>";
        });
    } else {
        resultats.innerHTML = "<p style ='color: red'>Not Found</p>"
    }
}


function appliquerAdresse(displayName, elementLat, elementLon) {

    // Supprimez l'ancien marqueur
    map.removeLayer(marker);

    // Créez un nouveau marqueur à partir de l'adresse donnée
    marker = L.marker([elementLat, elementLon]).addTo(map);

    // Utilisez une expression régulière pour extraire le numéro de rue, le mot dérivé (s'il existe) et le nom de la rue/lieu
    let rueMatch = displayName.match(/(\d+\s*(?:[Bb]is|[Tt]er)?)(?:,\s*)?(.*?)\,/);
    let rue = '';

    if (rueMatch) {
        const numeroRue = rueMatch[1] ? rueMatch[1].trim() : '';
        const nomRue = rueMatch[2].trim();

        rue = numeroRue + ' ' + nomRue;
    }

    // Mettre à jour la latitude et la longitude dans le formulaire
    let latitudeText = document.getElementById("lieu_latitude");
    let longitudeText = document.getElementById("lieu_longitude");
    let rueText = document.getElementById("lieu_rue");
    latitudeText.value = elementLat;
    longitudeText.value = elementLon;
    console.log(rue);
    rueText.value = rue;

    // Mettre à jour la map
    map.flyTo([elementLat, elementLon]);

}