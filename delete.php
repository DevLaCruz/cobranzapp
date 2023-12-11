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

// Verifica si el usuario es un administrador o el propietario del sector
if ($_SESSION["user"]["role"] !== "admin" && $sector["user_id"] !== $_SESSION["user"]["id"]) {
    http_response_code(403);
    echo("HTTP 403 UNAUTHORIZED");
    return;
}

// Verificación de confirmación para la eliminación
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_delete"]) && $_POST["confirm_delete"] === "yes") {
    // Elimina el sector
    $conn->prepare("DELETE FROM sectors WHERE id = :id")->execute([":id" => $id]);

    $_SESSION["flash"] = ["message" => "Sector {$sector['name']} deleted."];

    header("Location: home.php");
    return;
}
?>

<?php require "partials/header.php" ?>

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Eliminar Sector</div>
                <div class="card-body">
                    <p>¿Está seguro de que desea eliminar el sector <?= $sector['name'] ?>?</p>
                    <form method="POST" action="delete.php?id=<?= $id ?>">
                        <input type="hidden" name="confirm_delete" value="yes">
                        <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                        <a href="home.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php" ?>
