<?php

require "database.php";

session_start();

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$account_id = $_GET['id'];

$account_statement = $conn->prepare("SELECT * FROM accounts WHERE id = :account_id");
$account_statement->bindParam(":account_id", $account_id);
$account_statement->execute();

if ($account_statement->rowCount() == 0) {
    header("Location: home.php");
    exit();
}

$account = $account_statement->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar el formulario de editar cuenta
    $account_name = $_POST["account_name"];
    $account_number = $_POST["account_number"];
    $initial_balance = $_POST["initial_balance"];

    $update_statement = $conn->prepare("UPDATE accounts SET account_name = :account_name, account_number = :account_number, initial_balance=:initial_balance WHERE id = :account_id");
    $update_statement->bindParam(":account_name", $account_name);
    $update_statement->bindParam(":account_number", $account_number);
    $update_statement->bindParam(":initial_balance", $initial_balance);
    $update_statement->bindParam(":account_id", $account_id);    
    $update_statement->execute();

    header("Location: view_accounts.php?sector_id={$account['sector_id']}");
    exit();
}

?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
    <h2>Editar Cuenta</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="account_name" class="form-label">Nombre de la Cuenta</label>
            <input type="text" class="form-control" id="account_name" name="account_name" value="<?= $account['account_name'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="account_number" class="form-label">Número de la Cuenta</label>
            <input type="text" class="form-control" id="account_number" name="account_number" value="<?= $account['account_number'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="initial_balance" class="form-label">Saldo de la Cuenta</label>
            <input type="text" class="form-control" id="initial_balance" name="initial_balance" value="<?= $account['initial_balance'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<?php require "partials/footer.php" ?>
