<?php

require "database.php";

session_start();

// Verificar la existencia del parámetro sector_id en la URL
if (!isset($_GET['sector_id'])) {
    header("Location: home.php");
    exit();
}

$sector_id = $_GET['sector_id'];

// Obtener información del sector
$sector_statement = $conn->prepare("SELECT * FROM sectors WHERE id = :sector_id");
$sector_statement->bindParam(":sector_id", $sector_id);
$sector_statement->execute();

if ($sector_statement->rowCount() == 0) {
    // El sector no existe
    error_log("El sector no existe");
    header("Location: home.php");
    exit();
}

$sector = $sector_statement->fetch(PDO::FETCH_ASSOC);

?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
    <h2>Cuentas del Sector: <?= $sector["name"] ?></h2>
    <a href="add_account.php?sector_id=<?= $sector_id ?>" class="btn btn-success mb-3">Agregar Cuenta</a>
    <form>
        <input type="text" id="searchInput" placeholder="Buscar...">
    </form>

    <table class="table" id="dataTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de la Cuenta</th>
                <th>Número de la Cuenta</th>
                <th>Saldo de la Cuenta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Obtener cuentas asociadas al sector
            $accounts_statement = $conn->prepare("SELECT * FROM accounts WHERE sector_id = :sector_id");
            $accounts_statement->bindParam(":sector_id", $sector_id);
            $accounts_statement->execute();
            $accounts = $accounts_statement->fetchAll(PDO::FETCH_ASSOC);

            if ($accounts_statement->rowCount() > 0):
                foreach ($accounts as $account): ?>
                    <tr>
                        <td><?= $account["id"] ?></td>
                        <td><?= $account["account_name"] ?></td>
                        <td><?= $account["account_number"] ?></td>
                        <td><?= $account["remaining_balance"] ?></td>
                        <td>
                            <a href="edit_account.php?id=<?= $account["id"] ?>" class="btn btn-warning">Editar</a>
                            <a href="view_payments.php?account_id=<?= $account["id"] ?>" class="btn btn-primary mb-2">Ver Pagos</a>
                            <a href="delete_account.php?id=<?= $account["id"] ?>" class="btn btn-danger">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="5">No hay cuentas asociadas a este sector.</td>
                </tr>
            <?php endif;
            ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function () {
    // Manejar la búsqueda mientras se escribe en el campo de búsqueda
    $("#searchInput").on("input", function () {
        // Obtener el término de búsqueda
        var searchTerm = $(this).val().toLowerCase();

        // Filtrar las filas de la tabla que coincidan con el término de búsqueda
        $("#dataTable tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
        });
    });
});
</script>

<?php require "partials/footer.php" ?>
