<?php
require "database.php";

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

// Verificar el rol del usuario
$user_role = $_SESSION["user"]["role"];

// Solo permitir acceso a administradores
if ($user_role !== "admin") {
    header("Location: home.php");
    return;
}

// Obtener la lista de sectores
$sectors_statement = $conn->query("SELECT * FROM sectors");
$sectors = $sectors_statement->fetchAll(PDO::FETCH_ASSOC);

// Inicializar variables
$selected_sector = isset($_GET['sector_id']) ? $_GET['sector_id'] : null;
$selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// Filtrar por sector si se ha seleccionado uno
$filter_condition = $selected_sector ? "AND ps.sector_id = $selected_sector" : "";

// Obtener cobros totales por día, semana o mes
$payments_statement = $conn->prepare("SELECT SUM(p.payment_amount) AS total_amount, p.payment_date, ps.sector_id
                                      FROM payments p
                                      JOIN payments_sectors ps ON p.id = ps.payment_id
                                      WHERE DATE(p.payment_date) = :selected_date $filter_condition
                                      GROUP BY p.payment_date, ps.sector_id");
$payments_statement->bindParam(":selected_date", $selected_date);
$payments_statement->execute();
$payments = $payments_statement->fetchAll(PDO::FETCH_ASSOC);

// Obtener los nombres de los sectores para la lista desplegable
$sector_names = array_column($sectors, 'name', 'id');

?>

<?php require "partials/header.php"; ?>

<div class="container pt-5">
    <h2>Informe de Cobros</h2>

    <form method="GET">
        <div class="mb-3 row">
            <label for="sector_id" class="col-md-2 col-form-label">Sector</label>
            <div class="col-md-4">
                <select class="form-control" id="sector_id" name="sector_id">
                    <option value="">Todos los sectores</option>
                    <?php foreach ($sectors as $sector): ?>
                        <option value="<?= $sector["id"] ?>" <?= $selected_sector == $sector["id"] ? 'selected' : '' ?>>
                            <?= $sector["name"] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <label for="date" class="col-md-2 col-form-label">Fecha</label>
            <div class="col-md-4">
                <input type="date" class="form-control" id="date" name="date" value="<?= $selected_date ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Sector</th>
                <th>Cobros Totales</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= $payment["payment_date"] ?></td>
                    <td><?= $sector_names[$payment["sector_id"]] ?? 'Todos los sectores' ?></td>
                    <td><?= $payment["total_amount"] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php require "partials/footer.php"; ?>
