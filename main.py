#!/usr/bin/env python
from bs4 import BeautifulSoup
from flask import Flask, jsonify, render_template
import config
import json
import requests
from urllib import request

app = Flask(__name__)

BETASERIES_API_URL = 'https://api.betaseries.com/shows/member?&order=last_seen&limit=3&summary=true&id=' + config.betaseries_uid + '&key=' + config.betaseries_api_key
LASTFM_API_URL = 'http://ws.audioscrobbler.com/2.0/?method=user.gettopartists&period=1month&limit=3&format=json&user=' + config.lastfm_username + '&api_key=' + config.lastfm_api_key
GAMEKULT_URL = "https://www.gamekult.com/membre/1241161/collection/rechercher.html?&page=1"

@app.route('/')
def root():
    return render_template('index.html')

@app.route('/api/profile/lastfm')
def lastfm():
    json = requests.get(LASTFM_API_URL).json()
    artists = map(lambda x: x['name'], json['topartists']['artist'])
    return jsonify(list(artists))
    #return jsonify(list(['Artiste1', 'Artiste2', 'Artiste3']))

@app.route('/api/profile/betaseries')
def betaseries():
    json = requests.get(BETASERIES_API_URL).json()
    shows = map(lambda x: x['title'], json['shows'])
    return jsonify(list(shows))
    #return jsonify(list(['Show1', 'Show2', 'Show3']))

@app.route('/api/profile/gamekult')
def gamekult():
    html = requests.get(GAMEKULT_URL).text
    soup = BeautifulSoup(html, features="html.parser")
    html_figures = soup.find_all('figure')
    games = list(map(lambda figure:
        {'image_url': figure.img['src'], 'name': figure.next_sibling.next_sibling.h3.a.text}, html_figures))
    return jsonify(games[:3])

if __name__=='__main__':
    app.run(debug=True)
