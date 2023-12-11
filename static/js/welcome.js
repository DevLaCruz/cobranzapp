const navbar = document.querySelector(".navbar");
const welcome = document.querySelector(".welcome");
const navbarToggle = document.querySelector("#navbarNav");

const resizeBakgroundImg = () => {
  const height = window.innerHeight - navbar.clientHeight;
  welcome.style.height = `${height}px`;
};


navbarToggle.ontransitionend = resizeBakgroundImg;
navbarToggle.ontransitionstart = resizeBakgroundImg;
window.onresize = resizeBakgroundImg;
window.onload = resizeBakgroundImg;


// Contenido del archivo scripts.js
$(document).ready(function () {
  // Manejar la búsqueda cuando se envía el formulario
  $("#searchForm").submit(function (e) {
      e.preventDefault();

      // Obtener el término de búsqueda
      var searchTerm = $("#searchInput").val();

      // Realizar la solicitud AJAX al servidor
      $.post("search.php", { searchTerm: searchTerm }, function (data) {
          // Actualizar la tabla con los resultados
          var tableBody = $("#dataTable tbody");
          tableBody.empty();

          // Construir las filas de la tabla con los resultados
          $.each(JSON.parse(data), function (index, result) {
              var row = "<tr><td>" + result.id + "</td><td>" + result.nombre + "</td></tr>";
              tableBody.append(row);
          });
      });
  });
});
