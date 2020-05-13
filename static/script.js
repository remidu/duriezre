'use strict';

window.addEventListener('load', function () {
    this.updateMusic();
    this.updateShows();
    this.updateGames();
    this.updateComics();
    this.updateBeers();
});

function updateMusic() {
    fetch('/api/lastfm')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('artists')
        element.textContent = ' '.concat(json.map(album => album.artist.name).join(', '));
    }))
    .catch(error => console.error("Erreur : " + error));
}

function updateBeers() {
    fetch('/api/untappd')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('beers')
        element.textContent = ' : '.concat(json.map(beer => beer.name).join(', '));
    }))
    .catch(error => console.error("Erreur : " + error));
}

function updateComics() {
    fetch('/api/comicgeeks')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('comics')
        element.textContent = ' '.concat(json.map(book => book.name.replace(/ #([0-9])+/, '')).join(', '));
    }))
    .catch(error => console.error("Erreur : " + error));
}

function updateGames() {
    fetch('/api/gamekult')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('games')
        element.textContent = ' à '.concat(json.map(game => game.name).join(', '));
    }))
    .catch(error => console.error("Erreur : " + error));
}

function updateShows() {
    fetch('/api/betaseries')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('shows')
        element.textContent = ' '.concat(json.map(show => show.name.replace(/ \(([0-9])+\)/, '')).join(', '));
    }))
    .catch(error => console.error("Erreur : " + error));
}
