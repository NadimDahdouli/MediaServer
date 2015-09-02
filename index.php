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

$mIndexer = new MovieIndexer(Config::read("folder.movies"));
print_r($mIndexer->indexUnindexed());

foreach ($mIndexer->getIndexed() as $movie) {
    echo "$movie->Title\n<img src='$movie->Poster'></img>\n\n";
}

//
//function getTvMetadata($episode, &$matches) {
//    if (preg_match("'^(.+)S([0-9]+)E([0-9]+).*$'i", $episode, $matches)) {
//        array_shift($matches);
//        return $matches;
//    }
//    return FALSE;
//}
