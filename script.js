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

function toggleOpen(element) {
  element.classList.toggle("open");
}

const headerLinks = document.getElementById("header-links");
const root = document.querySelector(":root");
root.style.setProperty("--ul-height", `${headerLinks.clientHeight}px`);

window.addEventListener("resize", () => {
  root.style.setProperty("--ul-height", `${headerLinks.clientHeight}px`);
});

// Add .current class to header link matching current
const headerLinks_a = document.querySelectorAll("#header-links a");
headerLinks_a.forEach((a) => {
  if (a.href === window.location.href) {
    a.classList.add("current");
  }
});
