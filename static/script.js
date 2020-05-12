'use strict';

window.addEventListener('load', function () {
    console.log("Hello World!");
    this.updateArtists();
    this.updateComics();
    this.updateGames();
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

function updateComics() {
    fetch('/api/profile/comicgeeks')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('comics')
        element.textContent = ' '.concat(json.map(book => book.name).join(', '));
    }))
    .catch(error => console.error("Erreur : " + error));
}

function updateGames() {
    fetch('/api/profile/gamekult')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('games')
        element.textContent = ' à '.concat(json.map(game => game.name).join(', '));
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
