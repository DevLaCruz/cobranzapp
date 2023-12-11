<?php

require "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchTerm = $_POST["searchTerm"];

    // Realiza la búsqueda en la base de datos
    $searchStatement = $conn->prepare("SELECT * FROM tu_tabla WHERE nombre LIKE :searchTerm");
    $searchStatement->bindValue(":searchTerm", "%$searchTerm%");
    $searchStatement->execute();
    $results = $searchStatement->fetchAll(PDO::FETCH_ASSOC);

    // Retorna los resultados como JSON
    echo json_encode($results);
}

?>
