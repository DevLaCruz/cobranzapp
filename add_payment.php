<?php

require "database.php";

session_start();

if (!isset($_GET['account_id'])) {
    header("Location: home.php");
    exit();
}

$account_id = $_GET['account_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar el formulario de agregar pago
    $payment_date = $_POST["payment_date"];
    $payment_amount = $_POST["payment_amount"];

    // Obtener el saldo actual de la cuenta
    $account_statement = $conn->prepare("SELECT remaining_balance FROM accounts WHERE id = :account_id");
    $account_statement->bindParam(":account_id", $account_id);
    $account_statement->execute();
    $account = $account_statement->fetch(PDO::FETCH_ASSOC);

    $remaining_balance = $account["remaining_balance"] - $payment_amount;

    // Insertar el nuevo pago y actualizar el saldo de la cuenta
    $insert_statement = $conn->prepare("INSERT INTO payments (account_id, payment_date, payment_amount, remaining_balance) VALUES (:account_id, :payment_date, :payment_amount, :remaining_balance)");
    $insert_statement->bindParam(":account_id", $account_id);
    $insert_statement->bindParam(":payment_date", $payment_date);
    $insert_statement->bindParam(":payment_amount", $payment_amount);
    $insert_statement->bindParam(":remaining_balance", $remaining_balance);
    $insert_statement->execute();

    // Actualizar el saldo en la cuenta
    $update_account_statement = $conn->prepare("UPDATE accounts SET remaining_balance = :remaining_balance WHERE id = :account_id");
    $update_account_statement->bindParam(":remaining_balance", $remaining_balance);
    $update_account_statement->bindParam(":account_id", $account_id);
    $update_account_statement->execute();

    header("Location: view_payments.php?account_id=$account_id");
    exit();
}

?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
    <h2>Agregar Nuevo Pago para la Cuenta</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="payment_date" class="form-label">Fecha de Pago</label>
            <input type="date" class="form-control" id="payment_date" name="payment_date" required>
        </div>
        <div class="mb-3">
            <label for="payment_amount" class="form-label">Monto del Pago</label>
            <input type="text" class="form-control" id="payment_amount" name="payment_amount" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Pago</button>
    </form>
</div>

<?php require "partials/footer.php" ?>
