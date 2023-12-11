<?php
require "database.php";

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

// Inicializar la fecha seleccionada (puedes ajustar esto según tus necesidades)
$selected_date = isset($_POST["selected_date"]) ? $_POST["selected_date"] : date("Y-m-d");

// Consulta SQL para obtener la suma de los pagos por sector en la fecha seleccionada
$query = "
    SELECT
        s.id AS sector_id,
        s.name AS sector_name,
        SUM(p.payment_amount) AS total_payments
    FROM
        sectors s
    LEFT JOIN
        user_sector us ON s.id = us.sector_id
    LEFT JOIN
        users u ON us.user_id = u.id
    LEFT JOIN
        accounts a ON s.id = a.sector_id
    LEFT JOIN
        payments p ON a.id = p.account_id
    WHERE
        DATE(p.payment_date) = :selected_date
    GROUP BY
        s.id, s.name
";

$statement = $conn->prepare($query);
$statement->bindParam(":selected_date", $selected_date);
$statement->execute();

$results = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
    <h2>Reporte de Pagos por Sector en la Fecha: <?= $selected_date ?></h2>
    
    <form method="POST" action="report_for_day.php">
        <div class="mb-3 row">
            <label for="selected_date" class="col-md-2 col-form-label">Seleccionar Fecha</label>
            <div class="col-md-4">
                <input type="date" class="form-control" id="selected_date" name="selected_date" value="<?= $selected_date ?>">
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>ID del Sector</th>
                <th>Nombre del Sector</th>
                <th>Total de Pagos</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?= $result["sector_id"] ?></td>
                    <td><?= $result["sector_name"] ?></td>
                    <td><?= $result["total_payments"] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php require "partials/footer.php" ?>
