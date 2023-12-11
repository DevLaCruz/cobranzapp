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
    if (empty($_POST["name"]) || empty($_POST["phone_number"])) {
        $error = "Please fill all the fields.";
    } else if (strlen($_POST["phone_number"]) < 9) {
        $error = "Phone number must be at least 9 characters.";
    } else {
        $name = $_POST["name"];
        $phone_number = $_POST["phone_number"];

        $insert_statement = $conn->prepare("INSERT INTO sectors (name, phone_number) VALUES (:name, :phone_number)");
        $insert_statement->bindParam(":name", $name);
        $insert_statement->bindParam(":phone_number", $phone_number);
        $insert_statement->execute();

        $sector_id = $conn->lastInsertId();

        // Asignar cobradores al sector
        if (!empty($_POST["cobradores"])) {
            foreach ($_POST["cobradores"] as $cobrador_id) {
                $assignment_statement = $conn->prepare("INSERT INTO user_sector_assignment (user_id, sector_id) VALUES (:user_id, :sector_id)");
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
                            <label for="phone_number" class="col-md-4 col-form-label text-md-end">Número de Teléfono</label>
                            <div class="col-md-6">
                                <input id="phone_number" type="tel" class="form-control" name="phone_number" autocomplete="phone_number" autofocus>
                            </div>
                        </div>
                        <?php if ($user_role === "admin"): ?>
                            <div class="mb-3 row">
                                <label for="cobradores" class="col-md-4 col-form-label text-md-end">Cobradores Asignados</label>
                                <div class="col-md-6">
                                    <select multiple class="form-control" id="cobradores" name="cobradores[]">
                                        <?php foreach ($cobradores as $cobrador): ?>
                                            <option value="<?= $cobrador["id"] ?>"><?= $cobrador["name"] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif ?>
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
