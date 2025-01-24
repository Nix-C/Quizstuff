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

// function toggleOpen(element) {
//   element.classList.toggle("open");
// }

const header = document.getElementById("header");
const headerLinks = document.getElementById("header-links");
const dropdowns = document.querySelectorAll(".dropdown");
const root = document.querySelector(":root");

root.style.setProperty("--ul-height", `${headerLinks.clientHeight}px`);
root.style.setProperty("--header-height", `${header.clientHeight}px`);
window.addEventListener("resize", () => {
  root.style.setProperty("--ul-height", `${headerLinks.clientHeight}px`);
  root.style.setProperty("--header-height", `${header.clientHeight}px`);
});

function closeAllDropdowns() {
  dropdowns.forEach((dropdown) => {
    dropdown.classList.remove("open");
  });
}

function handleDropdownClick(e) {
  const currentDropdown = e.target.closest(".dropdown");

  // Close other dropdowns first
  closeAllDropdowns();

  // Toggle the current dropdown
  currentDropdown.classList.toggle("open");

  // Prevent the document click handler from closing this dropdown immediately
  e.stopPropagation();
}

document.addEventListener("click", (e) => {
  // Close all dropdowns if the click is outside of any dropdown
  closeAllDropdowns();
});

// Add click listeners to each dropdown
dropdowns.forEach((dropdown) => {
  const toggle = dropdown.querySelector(".dropdown-toggle");
  toggle.addEventListener("click", handleDropdownClick);
});

// Add .current class to header link matching current
const headerLinks_a = document.querySelectorAll("#header-links a");
headerLinks_a.forEach((a) => {
  if (a.href === window.location.href) {
    a.classList.add("current");
  }
});
