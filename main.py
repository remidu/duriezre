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

@app.route('/api/comicgeeks')
def comicgeeks():
    url = 'https://leagueofcomicgeeks.com/comic/get_comics?user_id=43509' \
        + '&list=1&date_type=recent&order=pulls'
    headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:124.0) Gecko/20100101 Firefox/124.0'}
    json = requests.get(url, headers=headers).json()
    html = json['list']
    soup = BeautifulSoup(html, features="html.parser")
    li_tags = soup.find_all('li')
    comics = list(map(lambda li:
        {'image': li.img['data-src'].replace('medium', 'large'),
            'url': 'https://leagueofcomicgeeks.com' + li.a['href'],
            'name': li.find('div', {'class': 'title'}).text.replace('\n', '')}, li_tags))
    return jsonify(comics[:3])

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

@app.route('/api/untappd')
def untappd():
    url = "https://untappd.com/user/remidu/beers?sort=date"
    html = requests.get(url, headers = {'User-Agent': 'Mozilla/5.0'}).text
    soup = BeautifulSoup(html, features="html.parser")
    beer_items = soup.find_all('div', {'class': 'beer-item'})
    beers = list(map(lambda item:
        {'image': item.a.img['src'], 'url': 'https://untappd.com' + item.a['href'],
            'name': item.a.img['alt'].replace(' label', '')}, beer_items))
    return jsonify(beers[:3])

if __name__=='__main__':
    app.run(debug=True)
