<?php

ini_set('max_execution_time', 600);
$response = exec('phantomjs scraper3.js');

$data = null;
if (file_exists('output.html')) {
//    $html = file_get_contents('output.html');
    $xmlPageXPath = returnXPathObject();

    $data = parsingXPathObject($xmlPageXPath);

} else {
    exit('Failed to open test.xml.');
}


$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "myDB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else
    echo "Connected successfully\n";

$sql = "CREATE TABLE IF NOT EXISTS calendario2 (
  COL1 varchar(17) DEFAULT NULL,
  COL2 varchar(17) DEFAULT NULL,
  giornata varchar(13) DEFAULT NULL,
  DATA_ varchar(17) DEFAULT NULL,
  E varchar(17) DEFAULT NULL,
  F varchar(17) DEFAULT NULL,
  G varchar(17) DEFAULT NULL,
  H varchar(17) DEFAULT NULL,
  I varchar(17) DEFAULT NULL,
  ID INT(5) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (ID)
) ";

if ($conn->query($sql) === TRUE) {
    echo "Table calendario2 created successfully\n";
} else {
    echo "Error creating table: \n" . $conn->error;
}

if($data!==null){
    saveSQL($conn, $data);
}

function returnXPathObject(){
    $xmlPageDom = new DOMDocument();
    @$xmlPageDom->loadHTMLFile("output.html");
    $xmlPageXPath = new DOMXPath($xmlPageDom);
    return $xmlPageXPath;

}

function parsingXPathObject($item){
    $data = array();

    $rows = $item->query("//table[@class='soccer']/tbody/tr");

    if($rows->length > 0){
        for($i=0; $i < $rows->length; $i++){
            $cols = $rows[$i]->getElementsByTagName('td');
            if ($cols->length === 1){
                $data[$i][0] = $cols[0]->textContent;
                $data[$i][1] = '';
                $data[$i][2] = '';
                $data[$i][3] = '';
                $data[$i][4] = '';
                $data[$i][5] = '';
                $data[$i][6] = '';
                $data[$i][7] = '';
                $data[$i][8] = '';
            }
            else{
                $data[$i][0] = $cols[1]->textContent;
                $data[$i][1] = $cols[2]->textContent;
                $data[$i][2] = $cols[3]->textContent;
                $data[$i][3] = $cols[4]->textContent;
                $data[$i][4] = '';
                $data[$i][5] = '';
                $data[$i][6] = '';
                $data[$i][7] = '';
                $data[$i][8] = '';
            }
        }


        return $data;
    }
    else{
        return null;
    }
}

function saveSQL($conn, $data){
    $stmt_insert = $conn->prepare("INSERT INTO calendario2 (COL1, COL2, giornata, DATA_, E, F, G, H, I) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("sssssssss", $COL1, $COL2, $giornata, $DATA_, $E, $F, $G, $H, $I);

    $stmt_select = $conn->prepare("SELECT `ID` FROM calendario2 WHERE COL1=? AND COL2=? AND giornata=? AND DATA_=? AND E=? AND F=? AND G=? AND H=? AND I=?");
    $stmt_select->bind_param("sssssssss", $COL1, $COL2, $giornata, $DATA_, $E, $F, $G, $H, $I);

    for ($i = 0; $i < count($data); $i++) {


        $COL1 = $data[$i][0];
        $COL2 = $data[$i][1];
        $giornata = $data[$i][2];
        $DATA_ = $data[$i][3];
        $E = $data[$i][4];
        $F = $data[$i][5];
        $G = $data[$i][6];
        $H = $data[$i][7];
        $I = $data[$i][8];

        if($stmt_select->execute()){
            $stmt_select->fetch();
            if ($stmt_select->num_rows===0){
                $stmt_insert->execute();
            }
        }
    }
    $stmt_select->close();
    $stmt_insert->close();
}


?>