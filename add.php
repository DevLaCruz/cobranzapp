<?php

require "database.php";

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

$error = null;

$user_role = $_SESSION["user"]["role"];

// Solo permitir el acceso a administradores
if ($user_role !== "admin") {
    header("Location: home.php");
    return;
}

// Obtener la lista de cobradores para asignar al sector
$cobradores_statement = $conn->query("SELECT * FROM users WHERE role = 'cobrador'");
$cobradores = $cobradores_statement->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"]) || empty($_POST["city"])) {
        $error = "Please fill all the fields.";
    } else if (strlen($_POST["city"]) < 4) {
        $error = "Phone number must be at least 9 characters.";
    } else {
        $name = $_POST["name"];
        $city = $_POST["city"];

        $insert_statement = $conn->prepare("INSERT INTO sectors (name, city) VALUES (:name, :city)");
        $insert_statement->bindParam(":name", $name);
        $insert_statement->bindParam(":city", $city);
        $insert_statement->execute();

        $sector_id = $conn->lastInsertId();

        // Asignar cobradores al sector
        if (!empty($_POST["cobradores"])) {
            foreach ($_POST["cobradores"] as $cobrador_id) {
                $assignment_statement = $conn->prepare("INSERT INTO user_sector(user_id, sector_id) VALUES (:user_id, :sector_id)");
                $assignment_statement->bindParam(":user_id", $cobrador_id);
                $assignment_statement->bindParam(":sector_id", $sector_id);
                $assignment_statement->execute();
            }
        }

        $_SESSION["flash"] = ["message" => "Sector $name added."];

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
                <div class="card-header">Agregar Nuevo Sector</div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <p class="text-danger"><?= $error ?></p>
                    <?php endif ?>
                    <form method="POST" action="add.php">
                        <div class="mb-3 row">
                            <label for="name" class="col-md-4 col-form-label text-md-end">Nombre</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" autocomplete="name" autofocus>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="city" class="col-md-4 col-form-label text-md-end">Ciudad</label>
                            <div class="col-md-6">
                                <input id="city" type="tel" class="form-control" name="city" autocomplete="city" autofocus>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Agregar Sector</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php" ?>
