<?php

require "database.php";

session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: login.php");
    return;
}

$sector_id = $_GET["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar la asignación del cobrador al sector
    $user_id = $_POST["user_id"];

    $insert_statement = $conn->prepare("INSERT INTO sector_cobrador_assignment (user_id, sector_id) VALUES (:user_id, :sector_id)");
    $insert_statement->bindParam(":user_id", $user_id);
    $insert_statement->bindParam(":sector_id", $sector_id);
    $insert_statement->execute();

    $_SESSION["flash"] = ["message" => "Cobrador asignado al sector correctamente."];

    header("Location: home.php");
    return;
}

// Obtener la lista de cobradores
$cobradores_statement = $conn->query("SELECT * FROM users WHERE role = 'cobrador'");
$cobradores = $cobradores_statement->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require "partials/header.php" ?>

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Asignar Cobrador al Sector</div>
                <div class="card-body">
                    <form method="POST" action="assign.php?id=<?= $sector_id ?>">
                        <div class="mb-3 row">
                            <label for="user_id" class="col-md-4 col-form-label text-md-end">Cobrador</label>
                            <div class="col-md-6">
                                <select id="user_id" class="form-control" name="user_id" required>
                                    <?php foreach ($cobradores as $cobrador): ?>
                                        <option value="<?= $cobrador["id"] ?>"><?= $cobrador["name"] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Asignar Cobrador</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php" ?>
