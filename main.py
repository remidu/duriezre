#!/usr/bin/env python
from bs4 import BeautifulSoup
from datetime import date, timedelta
from flask import Flask, jsonify, render_template
import requests

import config

app = Flask(__name__)

@app.route('/')
def root():
    return render_template('index.html')

@app.route('/api/profile/lastfm')
def lastfm():
    api_url = 'http://ws.audioscrobbler.com/2.0/?method=user.gettopartists' \
        + '&period=1month&limit=3&format=json&user=remidu&api_key=' + config.lastfm_api_key
    json = requests.get(api_url).json()
    artists = map(lambda x: x['name'], json['topartists']['artist'])
    return jsonify(list(artists))
    #return jsonify(list(['Artiste1', 'Artiste2', 'Artiste3']))

@app.route('/api/profile/betaseries')
def betaseries():
    api_url = 'https://api.betaseries.com/shows/member?id=23559' \
        + '&order=last_seen&summary=true&limit=3&key=' + config.betaseries_api_key
    json = requests.get(api_url).json()
    shows = map(lambda x: x['title'], json['shows'])
    return jsonify(list(shows))
    #return jsonify(list(['Show1', 'Show2', 'Show3']))

@app.route('/api/profile/comicgeeks')
def comicgeeks():
    three_months_ago = date.today() - timedelta(days=60)
    url = 'https://leagueofcomicgeeks.com/comic/get_comics?user_id=43509' \
        + '&list=1&date_type=recent&order=pulls&date=' + three_months_ago.isoformat()
    response = requests.get(url + '&list_filter=read')
    if response.headers['Content-Type'] == 'application/json':
        json = response.json()
    else:
        json = requests.get(url).json() # fetch unread comics
    html = json['list']
    soup = BeautifulSoup(html, features="html.parser")
    li_tags = soup.find_all('li')
    comics = list(map(lambda li:
        {'image_url': li.find('img')['data-original'], 'name': li.find('div', {'class': 'comic-title'}).text}, li_tags))
    return jsonify(comics[:3])

@app.route('/api/profile/gamekult')
def gamekult():
    url = "https://www.gamekult.com/membre/1241161/collection/rechercher.html?&page=1"
    html = requests.get(url).text
    soup = BeautifulSoup(html, features="html.parser")
    figure_tags = soup.find_all('figure')
    games = list(map(lambda figure:
        {'image_url': figure.img['src'], 'name': figure.next_sibling.next_sibling.h3.a.text}, figure_tags))
    return jsonify(games[:3])

if __name__=='__main__':
    app.run(debug=True)
