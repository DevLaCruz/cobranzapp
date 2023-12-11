    </main>
  </body>

<!-- Agrega este script al final de tu archivo HTML -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    document.body.classList.add("<?php echo $bodyClass; ?>");

    // Cambiar dinámicamente entre modos oscuro y claro
    document.getElementById("toggle-theme-btn").addEventListener("click", function () {
      document.body.classList.toggle("dark-mode");
      document.body.classList.toggle("light-mode");

      // Guarda la preferencia del usuario en una cookie (o localStorage/sessionStorage)
      var currentMode = document.body.classList.contains("dark-mode") ? "dark" : "light";
      document.cookie = "theme_mode=" + currentMode + "; path=/"; // Cambia "path" según la ruta de tu aplicación
    });
  });
</script>
</html>
