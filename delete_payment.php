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
    // Obtener el saldo actual de la cuenta asociada al pago
    $account_statement = $conn->prepare("SELECT remaining_balance FROM accounts WHERE id = :account_id");
    $account_statement->bindParam(":account_id", $payment["account_id"]);
    $account_statement->execute();
    $account = $account_statement->fetch(PDO::FETCH_ASSOC);

    // Calcular el nuevo saldo sumando el monto del pago que se va a eliminar
    $remaining_balance = $account["remaining_balance"] + $payment["payment_amount"];

    // Eliminar el pago
    $delete_payment_statement = $conn->prepare("DELETE FROM payments WHERE id = :payment_id");
    $delete_payment_statement->bindParam(":payment_id", $payment_id);
    $delete_payment_statement->execute();

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
    <h2>Eliminar Pago</h2>
    <p>¿Estás seguro de que deseas eliminar el pago con ID <?= $payment['id'] ?>?</p>
    <form method="POST">
        <button type="submit" class="btn btn-danger">Eliminar</button>
        <a href="view_payments.php?account_id=<?= $payment['account_id'] ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require "partials/footer.php" ?>
