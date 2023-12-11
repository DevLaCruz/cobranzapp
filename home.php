<?php

require "database.php";

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

$sectors = $conn->query("SELECT sectors.* FROM sectors
                        INNER JOIN user_sector ON sectors.id = user_sector.sector_id
                        WHERE user_sector.user_id = {$_SESSION['user']['id']}");

?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
    <div class="row">
        <?php
        // Obtener todos los sectores si el usuario es un administrador
        $isAdmin = $_SESSION["user"]["role"] === "admin";
        $sectorsQuery = $isAdmin ? "SELECT * FROM sectors" : "SELECT sectors.* FROM sectors INNER JOIN user_sector ON sectors.id = user_sector.sector_id WHERE user_sector.user_id = {$_SESSION['user']['id']}";
        $sectors = $conn->query($sectorsQuery);

        // Verificar si hay sectores
        if ($sectors->rowCount() == 0) {
            echo '<div class="col-md-4 mx-auto">
                    <div class="card card-body text-center">
                        <p>No se han encontrado sectores</p>';
            // Mostrar el enlace solo para usuarios que no son administradores
            if (!$isAdmin) {
                echo '<a href="add.php">Añade One!</a>';
            }
            echo '</div>
                  </div>';
        } else {
            // Mostrar los sectores
            foreach ($sectors as $sector) {
                echo '<div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="card-title text-capitalize">' . $sector["name"] . '</h3>
                                <p class="m-2">' . $sector["city"] . '</p>';
                
                // Mostrar opciones adicionales solo si el usuario es administrador
                if ($isAdmin) {
                    echo '<a href="edit.php?id=' . $sector["id"] . '" class="btn btn-secondary mb-2">Editar Sector</a>
                          <a href="delete.php?id=' . $sector["id"] . '" class="btn btn-danger mb-2">Eliminar Sector</a>
                          <a href="assign_sector.php?sector_id=' . $sector["id"] . '" class="btn btn-success mb-2">Asignar Sector</a>';
                }
                
                echo '<a href="view_accounts.php?sector_id=' . $sector["id"] . '" class="btn btn-primary mb-2">Ver Cuentas</a>
                      </div>
                    </div>
                  </div>';
            }
        }
        ?>
    </div>
</div>

<?php require "partials/footer.php" ?>


