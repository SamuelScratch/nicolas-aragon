<?php

global $db;

header('Content-Type: application/json; charset=utf-8');

$servername = 'localhost';
$dbname = 'nicolasafxadmin';
$password = 'h0NTnoTykVOXeSNOdV9b';
$username = 'nicolasafxadmin';
$dev_username = 'root';

try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $dev_username);
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connection successful";
} catch (PDOException $e) {
    // echo "Connection failed: " . $e->getMessage();
}

if (isset($_GET["cmd"])) {
    switch ($_GET["cmd"]) {
        case "getpublications":
            $obj = new stdClass();
            $obj->years = getYears();
            $obj->publications = getPublications();
            echo json_encode($obj);
            break;
    }
}

function getYears(){
    global $db;
    $requete = $db->prepare("SELECT year FROM publications GROUP BY year ORDER BY year DESC");
    $requete->execute();
    $years = $requete->fetchAll();
    return $years;
}

function getPublications(){
    global $db;
    $requete = $db->prepare("SELECT * FROM publications");
    $requete->execute();
    $publications = $requete->fetchAll();

    $publications_array = array();

    foreach($publications as $publication){
        array_push($publications_array, new Publication(
            $publication["id"],
            $publication["title"],
            $publication["year"],
            $publication["authors"],
            $publication["conference"]));
    }
    return $publications_array;
}


class Publication {
    public $id;
    public $title;
    public $year;
    public $authors;
    public $conference;
    public $extras;

    function __construct($p_id, $p_title, $p_year, $p_authors, $p_conference)
    {
        global $db;

        $this->id = $p_id;
        $this->title = $p_title;
        $this->year = $p_year;
        $this->authors = $p_authors;
        $this->conference = $p_conference;

        $this->extras = array();

        $requete = $db->prepare("SELECT publications_extras.* FROM publications_extras JOIN publications ON publications.id = publications_extras.id WHERE publications.id = :id");
        $requete->execute(array(
            "id" => $this->id
        ));
        $extras = $requete->fetchAll();

        foreach($extras as $extra){
            array_push($this->extras, new Extra(
                $extra["id"],
                $extra["type"],
                $extra["title"],
                $extra["content"],
                $extra["link"],
                $extra["publication"]
            ));
        }
    }
}

class Extra {
    public $id;
    public $type;
    public $title;
    public $content;
    public $link;
    public $publication;

    function __construct($p_id, $p_type, $p_title, $p_content, $p_link, $p_publication)
    {
        $this->id = $p_id;
        $this->type = $p_type;
        $this->title = $p_title;
        $this->content = $p_content;
        $this->link = $p_link;
        $this->publication = $p_publication;
    }
}