<?php

/**
 * Description of MovieIndexer
 *
 * @author Nadim Dahdouli
 */
class MovieIndexer {

    private $moviePath = NULL;
    private $db = NULL;

    public function __construct($moviePath) {
        $this->moviePath = $moviePath;

        $core = Core::getInstance();
        $this->db = $core->db;
    }

    public function getSingle($id) {
        $stmt = $this->db->query("SELECT * FROM movies WHERE _id = $id");
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getIndexed($order = "ORDER BY Title ASC") {
        $stmt = $this->db->query("SELECT * FROM movies $order");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getMovieTitles() {
        $movieTitles = array();
        foreach (glob("$this->moviePath\*") as $movieTitle) {
            $movieTitles[] .= basename($movieTitle);
        }
        return $movieTitles;
    }

    public function getMovieMetadata($movieTitle) {
        $json = file_get_contents("http://www.omdbapi.com/?t=" . urlencode(trim($movieTitle)) . "&plot=short&r=json");
        if ($json == FALSE)
            throw new Exception("Could not reach omdbapi.com");

        $metadata = json_decode($json);
        if ($metadata->Response == "False")
            throw new Exception("Could not get metadata for title $movieTitle");

        return $metadata;
    }

    public function indexUnindexed() {
        $failed = array();
        //$indexed = $this->getIndexed();

        foreach ($this->getUnIndexedFolders() as $movieTitle) {
            /*
              if (in_array($movieTitle, $indexed)) {
              continue;
              }
             */

            $metadata = NULL;
            try {
                $metadata = $this->getMovieMetadata($movieTitle);
            } catch (Exception $ex) {
                $failed[$movieTitle] = $ex->getMessage();
                continue;
            }

            // TODO: Add metadata to database
            $stmt = $this->db->prepare("INSERT INTO movies(folder, Title, Year, Rated, Released, Runtime, Genre, Director, Writer, Actors, Plot, Poster) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $movieTitle,
                $metadata->Title,
                $metadata->Year,
                $metadata->Rated,
                strtotime($metadata->Released),
                (int) $metadata->Runtime,
                $metadata->Genre,
                $metadata->Director,
                $metadata->Writer,
                $metadata->Actors,
                $metadata->Plot,
                $metadata->Poster
            ]);
        }

        if (count($failed) > 0) {
            return $failed;
        }

        return TRUE;
    }

    public function getIndexedFolders() {
        $indexed = array();

        $stmt = $this->db->query("SELECT folder FROM movies");
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $indexed[] .= $row->folder;
        }
        return $indexed;
    }

    public function getUnIndexedFolders() {
        // TODO: Get all titles on disk and compare with titles on database
        $indexed = $this->getIndexedFolders();
        $unindexed = array();
        foreach ($this->getMovieTitles() as $movieTitle) {
            if (in_array($movieTitle, $indexed)) {
                continue;
            }
            $unindexed[] .= $movieTitle;
        }
        return $unindexed;
    }

}
