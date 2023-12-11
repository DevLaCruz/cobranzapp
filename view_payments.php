<?php

require "database.php";

session_start();

if (!isset($_GET['account_id'])) {
    header("Location: home.php");
    exit();
}

$account_id = $_GET['account_id'];

// Obtener información de la cuenta
$account_statement = $conn->prepare("SELECT * FROM accounts WHERE id = :account_id");
$account_statement->bindParam(":account_id", $account_id);
$account_statement->execute();

if ($account_statement->rowCount() == 0) {
    header("Location: home.php");
    exit();
}

$account = $account_statement->fetch(PDO::FETCH_ASSOC);

// Obtener pagos asociados a la cuenta
$payments_statement = $conn->prepare("SELECT * FROM payments WHERE account_id = :account_id");
$payments_statement->bindParam(":account_id", $account_id);
$payments_statement->execute();
$payments = $payments_statement->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
    <h2>Detalle de Pagos para la Cuenta: <?= $account["account_name"] ?></h2>
    
    <p>Saldo Original: <?= $account["original_balance"] ?></p>

    <a href="add_payment.php?account_id=<?= $account_id ?>" class="btn btn-success mb-3">Agregar Pago</a>

    <?php if ($payments_statement->rowCount() > 0): ?>
        <table class="table">
            <!-- Encabezados de la tabla -->
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha de Pago</th>
                    <th>Monto del Pago</th>
                    <th>Saldo Restante</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas de la tabla para cada pago -->
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?= $payment["id"] ?></td>
                        <td><?= $payment["payment_date"] ?></td>
                        <td><?= $payment["payment_amount"] ?></td>
                        <td><?= $payment["remaining_balance"] ?></td>
                        <td>
                            <a href="edit_payment.php?id=<?= $payment["id"] ?>" class="btn btn-warning">Editar</a>
                            <a href="delete_payment.php?id=<?= $payment["id"] ?>" class="btn btn-danger">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay pagos asociados a esta cuenta.</p>
    <?php endif ?>
</div>

<?php require "partials/footer.php" ?>
