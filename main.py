#!/usr/bin/env python
from flask import Flask, jsonify, render_template
import config
import json
import requests

app = Flask(__name__)

BETASERIES_API_URL = 'https://api.betaseries.com/shows/member?&order=last_seen&limit=3&summary=true&id=' + config.betaseries_uid + '&key=' + config.betaseries_api_key
LASTFM_API_URL = 'http://ws.audioscrobbler.com/2.0/?method=user.gettopartists&period=1month&limit=3&format=json&user=' + config.lastfm_username + '&api_key=' + config.lastfm_api_key

@app.route('/')
def root():
    return render_template('index.html', artists=list(['Artiste1', 'Artiste2', 'Artiste3']), shows=list(['Show1', 'Show2', 'Show3']))

@app.route('/api/profile/lastfm')
def lastfm():
    response = requests.get(LASTFM_API_URL)
    content = json.loads(response.content.decode('utf-8'))
    artists = map(lambda x: x['name'], content['topartists']['artist'])
    return jsonify(list(artists))
    #return jsonify(list(['Artiste1', 'Artiste2', 'Artiste3']))

@app.route('/api/profile/betaseries')
def betaseries():
    response = requests.get(BETASERIES_API_URL)
    content = json.loads(response.content.decode('utf-8'))
    shows = map(lambda x: x['title'], content['shows'])
    return jsonify(list(shows))
    #return jsonify(list(['Show1', 'Show2', 'Show3']))

if __name__=='__main__':
    app.run(debug=True)
