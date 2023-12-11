<?php

require "database.php";

session_start();

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$payment_id = $_GET['id'];

// Obtener información del pago
$payment_statement = $conn->prepare("SELECT * FROM payments WHERE id = :payment_id");
$payment_statement->bindParam(":payment_id", $payment_id);
$payment_statement->execute();

if ($payment_statement->rowCount() == 0) {
    header("Location: home.php");
    exit();
}

$payment = $payment_statement->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar el formulario de editar pago
    $payment_date = $_POST["payment_date"];
    $payment_amount = $_POST["payment_amount"];

    // Obtener el saldo actual de la cuenta asociada al pago
    $account_statement = $conn->prepare("SELECT remaining_balance FROM accounts WHERE id = :account_id");
    $account_statement->bindParam(":account_id", $payment["account_id"]);
    $account_statement->execute();
    $account = $account_statement->fetch(PDO::FETCH_ASSOC);

    // Calcular el nuevo saldo restando el antiguo monto del pago y sumando el nuevo monto del pago
    $remaining_balance = $account["remaining_balance"] + $payment["payment_amount"] - $payment_amount;

    // Actualizar la información del pago
    $update_payment_statement = $conn->prepare("UPDATE payments SET payment_date = :payment_date, payment_amount = :payment_amount, remaining_balance = :remaining_balance WHERE id = :payment_id");
    $update_payment_statement->bindParam(":payment_date", $payment_date);
    $update_payment_statement->bindParam(":payment_amount", $payment_amount);
    $update_payment_statement->bindParam(":remaining_balance", $remaining_balance);
    $update_payment_statement->bindParam(":payment_id", $payment_id);
    $update_payment_statement->execute();

    // Actualizar el saldo en la cuenta asociada al pago
    $update_account_statement = $conn->prepare("UPDATE accounts SET remaining_balance = :remaining_balance WHERE id = :account_id");
    $update_account_statement->bindParam(":remaining_balance", $remaining_balance);
    $update_account_statement->bindParam(":account_id", $payment["account_id"]);
    $update_account_statement->execute();

    header("Location: view_payments.php?account_id={$payment['account_id']}");
    exit();
}

?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
    <h2>Editar Pago</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="payment_date" class="form-label">Fecha de Pago</label>
            <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?= $payment['payment_date'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="payment_amount" class="form-label">Monto del Pago</label>
            <input type="text" class="form-control" id="payment_amount" name="payment_amount" value="<?= $payment['payment_amount'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<?php require "partials/footer.php" ?>
