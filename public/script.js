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
    "pictures/palms.jpg",
    "pictures/Island-hero-3.png",
    "pictures/cozeaoverwiew.png",
    "pictures/water.jpg",
  ];

  let currentIndex = 0;

  setInterval(() => {
    heroImage.style.opacity = "0";

    setTimeout(() => {
      currentIndex = (currentIndex + 1) % images.length;
      heroImage.src = images[currentIndex];
      heroImage.style.opacity = "1";
    }, 700);
  }, 3000);
});

document.addEventListener("DOMContentLoaded", () => {
  const guestInput = document.querySelector('input[name="guest_name"]');
  const codeInput = document.querySelector('input[name="transfer_code"]');
  const button = document.getElementById("bookingButton");

  if (!guestInput || !codeInput || !button) return;

  const updateButton = () => {
    const hasGuest = guestInput.value.trim() !== "";

    if (hasGuest) {
      button.textContent = "Confirm booking";
      button.classList.add("confirm");
    } else {
      button.textContent = "Calculate total";
      button.classList.remove("confirm");
    }
  };

  guestInput.addEventListener("input", updateButton);
  codeInput.addEventListener("input", updateButton);
  updateButton();
});

document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("successModal");
  if (!modal) return;

  const closeModal = () => {
    modal.classList.remove("is-open");
  };

  modal.addEventListener("click", (event) => {
    const target = event.target;
    if (target && target.hasAttribute("data-close-modal")) {
      closeModal();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeModal();
  });
});
