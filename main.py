#!/usr/bin/env python
import os
from bs4 import BeautifulSoup
from flask import Flask, jsonify, render_template
import requests
import requests_cache

app = Flask(__name__)

requests_cache.install_cache('cache', backend='memory', expire_after=43200) # cache during 12h

@app.route('/')
def root():
    return render_template('index.html')

@app.route('/api/lastfm')
def lastfm():
    api_key = os.environ['LASTFM_API_KEY']
    api_url = 'http://ws.audioscrobbler.com/2.0/?method=user.gettopalbums&user=remidu' \
        + '&period=1month&limit=3&format=json&api_key=' + api_key
    json = requests.get(api_url).json()
    albums = map(lambda x:
        {'image': x['image'][3]['#text'], 'name': x['name'], 'url': x['url'],
            'artist': {'name': x['artist']['name'], 'url': x['artist']['url']}}, json['topalbums']['album'])
    return jsonify(list(albums))

@app.route('/api/betaseries')
def betaseries():
    api_key = os.environ['BETASERIES_API_KEY']
    shows_api_url = 'https://api.betaseries.com/shows/member?id=23559' \
        + '&order=last_seen&summary=true&limit=3&key=' + api_key
    shows_payload = requests.get(shows_api_url).json()
    ids = ','.join(map(lambda x: str(x['id']), shows_payload['shows']))
    details_api_url = 'https://api.betaseries.com/shows/display?id=' + ids \
        + '&key=' + api_key
    details_payload = requests.get(details_api_url).json()
    shows = map(lambda x,y:
        {'id': x['id'], 'image': x['images']['poster'], 'name': x['title'],
            'url': 'https://www.betaseries.com/serie/' + y['slug']}, shows_payload['shows'], details_payload['shows'])
    return jsonify(list(shows))

@app.route('/api/gamekult')
def gamekult():
    url = "https://www.gamekult.com/membre/1241161/collection/rechercher.html?&page=1"
    html = requests.get(url).text
    soup = BeautifulSoup(html, features="html.parser")
    figure_tags = soup.find_all('figure')
    games = list(map(lambda figure:
        {'image': figure.img['src'].replace('90_90', '220_220'),
            'url' : 'https://www.gamekult.com' + figure.a['href'],
            'name': figure.next_sibling.next_sibling.h3.a.text}, figure_tags))
    return jsonify(games[:3])

if __name__=='__main__':
    app.run(debug=True)
