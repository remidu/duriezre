<!DOCTYPE html>
<html>
    <head>
        <title>Duriez R&eacute;mi</title>
        <link type="text/css" rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>
            <h1><span class="machine">duriez.re</span><span class="human">mi</span></h1>
            <p><span>&#128188; D&eacute;veloppeur</span><span>&#128204 Lille</span></p>
        </header>

        <?php
		$config = include('config.php');
		?>

        <main>
            <div class="profile facebook">
                <div class="profile-display">
                    <div class="profile-desc">
                        <p>J'habite à Lille.</p>
                    </div>
                </div>
                <div class="profile-link">
                    <a href="https://www.facebook.com/duriez.remi" rel="me">
                        <span>Voir mon profil personnel sur Facebook</span>
                    </a>
                </div>
            </div>

            <div class="profile linkedin">
                <div class="profile-display">
                    <div class="profile-desc">
                        <p>Je suis développeur chez Ineat.</p>
                    </div>
                </div>
                <div class="profile-link">
                    <a href="https://www.linkedin.com/in/duriez" rel="me">
                        <span>Voir mon profil professionnel sur Linkedin</span>
                    </a>
                </div>
            </div>

            <?php
            function extract_album($user_album) {
                $album = new stdClass();
                $album->{'artist'} = $user_album->{'artist'}->{'name'};
                $album->{'name'} = $user_album->{'name'};
                $album->{'url'} = $user_album->{'url'};
                $album->{'image'} = $user_album->{'image'}[3]->{'#text'};
                return $album;
            }
            $payload = file_get_contents('http://ws.audioscrobbler.com/2.0/?method=user.gettopalbums&user=remidu&period=1month&limit=3&format=json&api_key='.$config['lastfm_api_key']);
            $payload = json_decode($payload);
            $albums = array_map('extract_album', $payload->{'topalbums'}->{'album'});
            $artists = array_map(fn($album): string => $album->{'artist'}, $albums);
            $artists = implode(', ', $artists);
            ?>

            <div class="profile lastfm">
                <div class="profile-display">
                    <div class="profile-desc">
                        <p>J'écoute <?=$artists?>…</p>
                        <div class="profile-samples">
                            <?php
                            foreach($albums as $album) {
                                echo '<a href="'.$album->{'url'}.'">
                                <img src="'.$album->{'image'}.'" title="'.$album->{'name'}.'">
                                </a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="profile-link">
                    <a href="https://www.last.fm/fr/user/RemiDu" rel="me">
                        <span>Voir mes artistes préférés sur Last.fm</span>
                    </a>
                </div>
            </div>

            <?php
            function extract_show($member_show, $show_detail) {
                $show = new stdClass();
                $show->{'url'} = 'https://www.betaseries.com/serie/'.$show_detail->{'slug'};
                $show->{'image'} = $member_show->{'images'}->{'poster'};
                $show->{'name'} = $member_show->{'title'};
                return $show;
            }

            function extract_id($member_show) {
                return $member_show->{'id'};
            }

            $key = $config['betaseries_api_key'];
            $shows_payload = file_get_contents('https://api.betaseries.com/shows/member?id=23559&order=last_seen&summary=true&limit=3&key='.$key);
            $shows_payload = json_decode($shows_payload);
            $ids = implode(',', array_map('extract_id', $shows_payload->{'shows'}));
            $details_payload = file_get_contents('https://api.betaseries.com/shows/display?id='.$ids.'&key='.$key);
            $details_payload = json_decode($details_payload);
            $shows = array_map('extract_show', $shows_payload->{'shows'}, $details_payload->{'shows'});
            $titles = array_map(fn($show): string => $show->{'name'}, $shows);
            $titles = implode(', ', $titles);
            ?>

            <div class="profile betaseries">
                <div class="profile-display">
                    <div class="profile-desc">
                        <p>Je regarde <?=$titles?>…</p>
                        <div class="profile-samples">
                            <?php
                            foreach($shows as $show) {
                                echo '<a href="'.$show->{'url'}.'">
                                <img src="'.$show->{'image'}.'" title="'.$show->{'name'}.'">
                                </a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="profile-link">
                    <a href="https://www.betaseries.com/membre/remi" rel="me">
                        <span>Voir mes séries en cours sur Betaseries</span>
                    </a>
                </div>
            </div>

            <?php
            function extract_book($node) {
                $book = new stdClass();
                $book->{'url'} = 'https://leagueofcomicgeeks.com'.$node->childNodes->item(1)->childNodes->item(1)->attributes->getNamedItem('href')->textContent;
                $book->{'image'} = str_replace('medium','large',$node->childNodes->item(1)->childNodes->item(1)->childNodes->item(1)->attributes->getNamedItem('data-src')->textContent);
                $book->{'name'} = $node->childNodes->item(7)->childNodes->item(1)->textContent;
                return $book;
            }

            $payload = file_get_contents('https://leagueofcomicgeeks.com/comic/get_comics?user_id=43509&list=1&date_type=recent&order=pulls');
            $payload = json_decode($payload);
            $doc = new DomDocument();
            $doc->loadHTML($payload->{'list'}, LIBXML_NOERROR);
            $xpath = new DOMXPath($doc);
            $list = $xpath->query('//li');
            for ($i = 0; $i < 3; $i++) {
                $comics[$i] = extract_book($list->item($i));
            }
            $titles = array_map(fn($book): string => preg_replace('/ #([0-9])+/', '', $book->{'name'}), $comics);
            $titles = implode(', ', $titles);
            ?>

            <div class="profile comicgeeks">
                <div class="profile-display">
                    <div class="profile-desc">
                        <p>Je lis <?=$titles?>…</p>
                        <div class="profile-samples">
                            <?php
                            foreach($comics as $book) {
                                echo '<a href="'.$book->{'url'}.'">
                                <img src="'.$book->{'image'}.'" title="'.$book->{'name'}.'">
                                </a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="profile-link">
                    <a href="https://leagueofcomicgeeks.com/profile/remi" rel="me">
                        <span>Voir ma liste sur Comic Geeks</span>
                    </a>
                </div>
            </div>

            <?php
            function extract_game($node) {
                $game = new stdClass();
                $game->{'url'} = 'https://www.gamekult.com'.$node->childNodes->item(1)->attributes->getNamedItem('href')->textContent;
                $game->{'image'} = str_replace('90_90','220_220',$node->childNodes->item(1)->childNodes->item(1)->attributes->getNamedItem('src')->textContent);
                $game->{'name'} = $node->childNodes->item(1)->childNodes->item(1)->attributes->getNamedItem('alt')->textContent;
                return $game;
            }		

            $payload = file_get_contents('https://www.gamekult.com/membre/1241161/collection/rechercher.html?&page=1');
            $payload = str_replace('figure','h6',$payload);
            $doc = new DomDocument();
            $doc->loadHTML($payload, LIBXML_NOERROR);
            $xpath = new DOMXPath($doc);
            $list = $xpath->query('//h6');
            for ($i = 0; $i < 3; $i++) {
                $games[$i] = extract_game($list->item($i));
            }
            $titles = array_map(fn($game): string => $game->{'name'}, $games);
            $titles = implode(', ', $titles);
            ?>

            <div class="profile gamekult">
                <div class="profile-display">
                    <div class="profile-desc">
                        <p>Je joue à <?=$titles?>…</p>
                        <div class="profile-samples">
                            <?php
                            foreach($games as $game) {
                                echo '<a href="'.$game->{'url'}.'">
                                <img src="'.$game->{'image'}.'" title="'.$game->{'name'}.'">
                                </a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="profile-link">
                    <a href="https://www.gamekult.com/membre/remidu-1241161/collection" rel="me">
                        <span>Voir ma collection sur Gamekult</span>
                    </a>
                </div>
            </div>

            <?php
            function extract_beer($node) {
                $beer = new stdClass();
                $beer->{'url'} = 'https://untappd.com'.$node->childNodes->item(1)->attributes->getNamedItem('href')->textContent;
                $beer->{'image'} = $node->childNodes->item(1)->childNodes->item(1)->attributes->getNamedItem('data-original')->textContent;
                $beer->{'name'} = $node->childNodes->item(2)->childNodes->item(1)->childNodes->item(0)->textContent;
                return $beer;
            }

            $payload = file_get_contents('https://untappd.com/user/remidu/beers?sort=date');
            $doc = new DomDocument();
            $doc->loadHTML($payload, LIBXML_NOERROR);
            $xpath = new DOMXPath($doc);
            $list = $xpath->query('//*/div[@class="beer-item"]');
            for ($i = 0; $i < 3; $i++) {
                $beers[$i] = extract_beer($list->item($i));
            }
            $names = array_map(fn($beer): string => $beer->{'name'}, $beers);
            $names = implode(', ', $names);
            ?>

            <div class="profile untappd">
                <div class="profile-display">
                    <div class="profile-desc">
                        <p>Je bois des bières : <?=$names?>…</p>
                        <div class="profile-samples">
                            <?php
                            foreach($beers as $beer) {
                                echo '<a href="'.$beer->{'url'}.'">
                                <img src="'.$beer->{'image'}.'" title="'.$beer->{'name'}.'">
                                </a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="profile-link">
                    <a href="https://untappd.com/user/remidu" rel="me">
                        <span>Voir mon palmarès sur Untappd</span>
                    </a>
                </div>
            </div>
        </main>
    </body>
</html>
