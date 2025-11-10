// toggle collapse kapag click sa logo
document.getElementById("logo").addEventListener("click", function() {
  document.querySelector(".sidebar").classList.toggle("collapsed");
});

// toggle dropdown menu kapag mobile hamburger menu
document.getElementById("menuToggle").addEventListener("click", function() {
  document.querySelector(".sidebar").classList.toggle("active");
});
