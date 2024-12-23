var sticky = document.getElementById("sticky");

document.addEventListener("scroll", () => {
  if (window.scrollY > 90) {
    sticky.style.position = "fixed";
    sticky.style.top = "0px";
  } else {
    sticky.style.position = "relative";
  }
});

const currentYear = new Date().getFullYear();
document.querySelectorAll(".current-year").forEach((e) => {
  e.innerHTML = currentYear;
});
