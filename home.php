<?php
require "database.php";

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Verificar el tipo de usuario
$is_admin = $_SESSION["user"]["is_admin"]; // Asumiendo que hay un campo "is_admin" en la tabla "users"

// Obtener sectores según el tipo de usuario
if ($is_admin) {
    // Si es admin, obtener todos los sectores
    $sectors_statement = $conn->query("SELECT * FROM sectors WHERE user_id = {$_SESSION['user']['id']}");
} else {
    // Si es cobrador, obtener solo los sectores asignados
    $sectors_statement = $conn->query("SELECT s.* FROM sectors s JOIN user_sector_assignment ua ON s.id = ua.sector_id WHERE ua.user_id = {$_SESSION['user']['id']}");
}

$sectors = $sectors_statement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
    <div class="row">
        <?php if (count($sectors) == 0): ?>
            <div class="col-md-4 mx-auto">
                <div class="card card-body text-center">
                    <p>No se han encontrado sectores</p>
                    <?php if ($is_admin): ?>
                        <a href="add.php">Añade Uno!</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($sectors as $sector): ?>
                <div class="col-md-4 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title text-capitalize"><?= $sector["name"] ?></h3>
                            <p class="m-2"><?= $sector["phone_number"] ?></p>
                            <?php if ($is_admin): ?>
                                <a href="edit.php?id=<?= $sector["id"] ?>" class="btn btn-secondary mb-2">Editar Sector</a>
                                <a href="delete.php?id=<?= $sector["id"] ?>" class="btn btn-danger mb-2">Eliminar Sector</a>
                            <?php endif; ?>
                            <a href="view_accounts.php?sector_id=<?= $sector["id"] ?>" class="btn btn-primary mb-2">Ver Cuentas</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require "partials/footer.php" ?>
