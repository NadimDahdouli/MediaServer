<?php

set_time_limit(0);

function __autoload($classname) {
    include_once "$classname.php";
}

// Config
Config::write("db.host", "localhost");
Config::write("db.dbname", "mediaserver");
Config::write("db.user", "mediaserver");
Config::write("db.password", "qFw5Y4QxpQWGNseh");

Config::write("folder.movies", "D:\Videos\Movies");

echo "<pre>";

/*
  foreach (glob("TV/*") as $show) {
  $showTitle = preg_replace("/^(.+\/)/i", "", $show);
  foreach(glob("$show/*") as $season) {
  print_r($season."\n");
  }
  }
 */



$mIndexer = new MovieIndexer(Config::read("folder.movies"));
print_r($mIndexer->indexUnindexed());

foreach ($mIndexer->getIndexed() as $movie) {
    echo "$movie->Title\n<img src='$movie->Poster'></img>\n\n";
}

//print_r($mIndexer->getIndexed());

/*
  $movieTitles = $mIndexer->getMovieTitles();
  foreach ($movieTitles as $movieTitle) {
  echo $movieTitle."\n";
  print_r($mIndexer->getMovieMetadata($movieTitle));
  echo "\n\n";
  }
  print_r(NULL);
 */

/*
  foreach ($tv as $show) {
  foreach ($show as $season) {
  foreach ($season as $episode) {
  $matches = array();
  if (getTvMetadata($episode, $matches)) {
  $title = urlencode(trim($matches[0]));
  $json = file_get_contents("http://omdbapi.com/?t=$title&Season=$matches[1]&Episode=$matches[2]");
  echo "$matches[0] $matches[1] $matches[2]\n";
  $details = json_decode($json);

  if ($details->Response == "True") {
  echo $details->Title . "\n\n";
  }
  }
  }
  }
  }
 */

function getTvMetadata($episode, &$matches) {
    if (preg_match("'^(.+)S([0-9]+)E([0-9]+).*$'i", $episode, $matches)) {
        array_shift($matches);
        return $matches;
    }
    return FALSE;
}
