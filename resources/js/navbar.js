document.addEventListener("DOMContentLoaded", () => {
    const menuButton = document.getElementById("menu-toggle");
    const navLinks = document.getElementById("nav-links");
  
    menuButton.addEventListener("click", () => {
      navLinks.classList.toggle("hidden");
    });
});
