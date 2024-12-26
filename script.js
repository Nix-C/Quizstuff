// const header = document.getElementById("nav");

// document.addEventListener("scroll", () => {
//   if (window.scrollY > 90) {
//     nav.classList.add("sticky");
//   } else {
//     nav.classList.remove("sticky");
//   }
// });

const currentYear = new Date().getFullYear();
document.querySelectorAll(".current-year").forEach((e) => {
  e.innerHTML = currentYear;
});

function toggleNavOpen() {
  const body = document.querySelector("body");
  body.classList.toggle("nav-open");
}
