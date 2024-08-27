/*!
* Start Bootstrap - Shop Homepage v5.0.6 (https://startbootstrap.com/template/shop-homepage)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-shop-homepage/blob/master/LICENSE)
*/
// This file is intentionally blank
// Use this file to add JavaScript to your project
document.addEventListener('DOMContentLoaded', function () {
    // Aumentar tamaño de fuente
    document.getElementById('increase-font').addEventListener('click', function (e) {
      e.preventDefault();
      document.body.style.fontSize = 'larger';
    });
  
    // Reducir tamaño de fuente
    document.getElementById('decrease-font').addEventListener('click', function (e) {
      e.preventDefault();
      document.body.style.fontSize = 'smaller';
    });
  
    // Cambiar tema
    document.getElementById('toggle-theme').addEventListener('click', function (e) {
      e.preventDefault();
      document.body.classList.toggle('dark-theme');
    });
  });
  