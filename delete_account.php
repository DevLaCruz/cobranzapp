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
    // Procesar la eliminación de la cuenta
    $delete_statement = $conn->prepare("DELETE FROM accounts WHERE id = :account_id");
    $delete_statement->bindParam(":account_id", $account_id);
    $delete_statement->execute();

    header("Location: view_accounts.php?sector_id={$account['sector_id']}");
    exit();
}

?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
    <h2>Eliminar Cuenta</h2>
    <p>¿Estás seguro de que deseas eliminar la cuenta <strong><?= $account['account_name'] ?></strong>?</p>
    <form method="POST">
        <button type="submit" class="btn btn-danger">Eliminar</button>
        <a href="view_accounts.php?sector_id=<?= $account['sector_id'] ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require "partials/footer.php" ?>
