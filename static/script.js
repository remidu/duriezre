'use strict';

window.addEventListener('load', function () {
    this.updateMusic();
    this.updateShows();
    this.updateGames();
});

function updateMusic() {
    let element = document.getElementById('artists');
    fetch('/api/lastfm')
    .then(response => response.json()
    .then(json => {
        let names = json.map(album => album.artist.name);
        element.textContent = [...new Set(names)].join(', ');
        json.forEach(album =>
            this.addImgToProfile('lastfm', album.image, album.artist.name + " – " + album.name, album.url));
    }))
    .catch(error => {
        console.error("Erreur : " + error);
        element.textContent = ' de la musique';
    });
}

function updateGames() {
    let element = document.getElementById('games');
    fetch('/api/gamekult')
    .then(response => response.json()
    .then(json => {
        element.textContent = json.map(game => game.name).join(', ');
        json.forEach(game =>
            this.addImgToProfile('gamekult', game.image, game.name, game.url));
    }))
    .catch(error => {
        console.error("Erreur : " + error);
        element.textContent = 'des jeux vidéo';
    });
}

function updateShows() {
    let element = document.getElementById('shows');
    fetch('/api/betaseries')
    .then(response => response.json()
    .then(json => {
        element.textContent = json.map(show => show.name.replace(/ \(([0-9])+\)/, '')).join(', ');
        json.forEach(show =>
            this.addImgToProfile('betaseries', show.image, show.name, show.url));
    }))
    .catch(error => {
        console.error("Erreur : " + error);
        element.textContent = ' des séries télé';
    });
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
            .appendChild(a);
}
