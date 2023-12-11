<?php

require "database.php";

session_start();

if (!isset($_GET['sector_id'])) {
    header("Location: home.php");
    exit();
}

$sector_id = $_GET['sector_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar el formulario de agregar cuenta
    $account_name = $_POST["account_name"];
    $account_number = $_POST["account_number"];
    $remaining_balance = $_POST["remaining_balance"];

    // Establecer original_balance igual a remaining_balance si no hay pagos
    $original_balance = $remaining_balance;

    $insert_statement = $conn->prepare("INSERT INTO accounts (sector_id, account_name, account_number, remaining_balance, original_balance) VALUES (:sector_id, :account_name, :account_number, :remaining_balance, :original_balance)");
    $insert_statement->bindParam(":sector_id", $sector_id);
    $insert_statement->bindParam(":account_name", $account_name);
    $insert_statement->bindParam(":account_number", $account_number);
    $insert_statement->bindParam(":remaining_balance", $remaining_balance);
    $insert_statement->bindParam(":original_balance", $original_balance);

    $insert_statement->execute();

    header("Location: view_accounts.php?sector_id=$sector_id");
    exit();
}

?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
    <h2>Agregar Nueva Cuenta</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="account_name" class="form-label">Nombre de la Cuenta</label>
            <input type="text" class="form-control" id="account_name" name="account_name" required>
        </div>
        <div class="mb-3">
            <label for="account_number" class="form-label">Número de la Cuenta</label>
            <input type="text" class="form-control" id="account_number" name="account_number" required>
        </div>
        <div class="mb-3">
            <label for="remaining_balance" class="form-label">Saldo de la Cuenta</label>
            <input type="text" class="form-control" id="remaining_balance" name="remaining_balance" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Cuenta</button>
    </form>
</div>

<?php require "partials/footer.php" ?>
