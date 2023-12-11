<?php

require "database.php";

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

$user_role = $_SESSION["user"]["role"];

if ($user_role !== "admin") {
    header("Location: home.php");
    return;
}

$sector_id = $_GET["sector_id"];
$sector = $conn->query("SELECT * FROM sectors WHERE id = $sector_id")->fetch(PDO::FETCH_ASSOC);

$cobradores_statement = $conn->query("SELECT * FROM users WHERE role = 'cobrador'");
$cobradores = $cobradores_statement->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y procesar la asignación de usuario al sector
    if (!empty($_POST["cobrador_id"])) {
        $cobrador_id = $_POST["cobrador_id"];

        $assignment_statement = $conn->prepare("INSERT INTO user_sector (user_id, sector_id) VALUES (:user_id, :sector_id)");
        $assignment_statement->bindParam(":user_id", $cobrador_id);
        $assignment_statement->bindParam(":sector_id", $sector_id);
        $assignment_statement->execute();

        $_SESSION["flash"] = ["message" => "Sector asignado a usuario exitosamente."];
        header("Location: home.php");
        return;
    }
}

?>

<?php require "partials/header.php" ?>

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Asignar Sector</div>
                <div class="card-body">
                    <form method="POST" action="assign_sector.php?sector_id=<?= $sector_id ?>">
                        <div class="mb-3 row">
                            <label for="cobrador_id" class="col-md-4 col-form-label text-md-end">Cobrador</label>
                            <div class="col-md-6">
                                <select class="form-control" id="cobrador_id" name="cobrador_id">
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
