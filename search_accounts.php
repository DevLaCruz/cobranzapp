<?php

require "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchTerm = $_POST["searchTerm"];
    $sector_id = $_POST["sector_id"];

    // Realiza la búsqueda en la base de datos
    $searchStatement = $conn->prepare("SELECT * FROM accounts WHERE sector_id = :sector_id AND account_name LIKE :searchTerm");
    $searchStatement->bindParam(":sector_id", $sector_id);
    $searchStatement->bindValue(":searchTerm", "%$searchTerm%");
    $searchStatement->execute();
    $results = $searchStatement->fetchAll(PDO::FETCH_ASSOC);

    // Retorna los resultados como JSON
    echo json_encode($results);
}

?>
