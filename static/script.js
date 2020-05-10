'use strict';

window.addEventListener('load', function () {
    console.log("Hello World!");
    this.updateArtists();
    this.updateShows();
});

function updateArtists() {
    fetch('/api/profile/lastfm')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('artists')
        element.textContent = ' '.concat(json.join(', '));
    }))
    .catch(error => console.error("Erreur : " + error));
}

function updateShows() {
    fetch('/api/profile/betaseries')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('shows')
        element.textContent = ' '.concat(json.join(', '));
    }))
    .catch(error => console.error("Erreur : " + error));
}
