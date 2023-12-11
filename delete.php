<?php

require "database.php";

session_start();

if (!isset($_SESSION["user"])) {
  header("Location: login.php");
  return;
}

$id = $_GET["id"];

$statement = $conn->prepare("SELECT * FROM sectors WHERE id = :id LIMIT 1");
$statement->execute([":id" => $id]);

if ($statement->rowCount() == 0) {
  http_response_code(404);
  echo("HTTP 404 NOT FOUND");
  return;
}

$sector = $statement->fetch(PDO::FETCH_ASSOC);

if ($sector["user_id"] !== $_SESSION["user"]["id"]) {
  http_response_code(403);
  echo("HTTP 403 UNAUTHORIZED");
  return;
}

$conn->prepare("DELETE FROM sectors WHERE id = :id")->execute([":id" => $id]);

$_SESSION["flash"] = ["message" => "sector {$sector['name']} deleted."];

header("Location: home.php");
