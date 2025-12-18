document.addEventListener("DOMContentLoaded", () => {
  const scrollTopButton = document.getElementById("scrollTopButton");

  if (!scrollTopButton) {
    return;
  }

  scrollTopButton.addEventListener("click", () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const heroImage = document.querySelector(".hero-image-img");

  if (!heroImage) {
    return;
  }

  const images = [
    "pictures/island-hero-1.png",
    "pictures/island-hero-3.png",
    "pictures/cozeaoverwiew.png",
    // "/pictures/island-hero-2.png",
  ];

  let currentIndex = 0;

  setInterval(() => {
    heroImage.style.opacity = "0";

    setTimeout(() => {
      currentIndex = (currentIndex + 1) % images.length;
      heroImage.src = images[currentIndex];
      heroImage.style.opacity = "1";
    }, 700);
  }, 4000);
});
