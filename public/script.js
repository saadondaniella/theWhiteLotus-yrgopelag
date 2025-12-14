const scrollTopButton = document.getElementById("scrollTopButton");

scrollTopButton.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
});
