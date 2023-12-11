<?php

require "database.php";

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

$id = $_GET["id"];

$statement = $conn->prepare("SELECT id, name, city FROM sectors WHERE id = :id LIMIT 1");
$statement->execute([":id" => $id]);

if ($statement->rowCount() == 0) {
    http_response_code(404);
    echo("HTTP 404 NOT FOUND");
    return;
}

$sector = $statement->fetch(PDO::FETCH_ASSOC);

if ($_SESSION["user"]["role"] !== "admin" && $sector["id"] !== $id) {
  http_response_code(403);
  echo("HTTP 403 UNAUTHORIZED");
  return;
}


$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"]) || empty($_POST["city"])) {
        $error = "Please fill all the fields.";
    } else if (strlen($_POST["city"]) < 9) {
        $error = "Phone number must be at least 9 characters.";
    } else {
        $name = $_POST["name"];
        $city = $_POST["city"];

        $update_statement = $conn->prepare("UPDATE sectors SET name = :name, city = :city WHERE id = :id");
        $update_statement->execute([
            ":id" => $id,
            ":name" => $name,
            ":city" => $city,
        ]);

        $_SESSION["flash"] = ["message" => "Sector $name editado."];

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
        <div class="card-header">Editar Sector</div>
        <div class="card-body">
          <?php if ($error): ?>
            <p class="text-danger">
              <?= $error ?>
            </p>
          <?php endif ?>
          <form method="POST" action="edit.php?id=<?= $id ?>">
            <div class="mb-3 row">
              <label for="name" class="col-md-4 col-form-label text-md-end">Nombre</label>
              <div class="col-md-6">
                <input value="<?= $sector['name'] ?>" id="name" type="text" class="form-control" name="name" autocomplete="name" autofocus>
              </div>
            </div>
            <div class="mb-3 row">
              <label for="city" class="col-md-4 col-form-label text-md-end">Ciudad</label>
              <div class="col-md-6">
                <input value="<?= $sector['city'] ?>" id="city" type="tel" class="form-control" name="city" autocomplete="city" autofocus>
              </div>
            </div>
            <div class="mb-3 row">
              <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require "partials/footer.php" ?>
