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
        json.forEach(album =>
            this.addImgToProfile('lastfm', album.image, album.artist.name + " – " + album.name, album.url));
    }))
    .catch(error => console.error("Erreur : " + error));
}

function updateBeers() {
    fetch('/api/untappd')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('beers')
        element.textContent = ' : '.concat(json.map(beer => beer.name).join(', '));
        json.forEach(beer =>
            this.addImgToProfile('untappd', beer.image, beer.name, beer.url));
    }))
    .catch(error => console.error("Erreur : " + error));
}

function updateComics() {
    fetch('/api/comicgeeks')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('comics');
        let names = json.map(book => book.name.replace(/ #([0-9])+/, ''));
        element.textContent = ' '.concat([...new Set(names)].join(', '));
        json.forEach(book =>
            this.addImgToProfile('comicgeeks', book.image, book.name, book.url));
    }))
    .catch(error => console.error("Erreur : " + error));
}

function updateGames() {
    fetch('/api/gamekult')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('games')
        element.textContent = ' à '.concat(json.map(game => game.name).join(', '));
        json.forEach(game =>
            this.addImgToProfile('gamekult', game.image, game.name, game.url));
    }))
    .catch(error => console.error("Erreur : " + error));
}

function updateShows() {
    fetch('/api/betaseries')
    .then(response => response.json()
    .then(json => {
        let element = document.getElementById('shows')
        element.textContent = ' '.concat(json.map(show => show.name.replace(/ \(([0-9])+\)/, '')).join(', '));
        json.forEach(show =>
            this.addImgToProfile('betaseries', show.image, show.name)); // TODO add url
    }))
    .catch(error => console.error("Erreur : " + error));
}

function addImgToProfile(profileName, image, title, url) {
    let a = document.createElement('a');
    a.href = url
    let img = document.createElement('img');
    img.title = title;
    img.src = image;
    a.appendChild(img);
    document.getElementsByClassName(profileName)[0]
        .getElementsByClassName('profile-samples')[0]
            .appendChild(url ? a : img);
}
