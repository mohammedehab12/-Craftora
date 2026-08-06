// Craftora Main JavaScript

document.addEventListener("DOMContentLoaded", function () {
  // =========================
  // Add To Cart
  // =========================

  document.querySelectorAll(".add-to-cart").forEach((button) => {
    button.addEventListener("click", function () {
      const productId = this.dataset.id;

      addToCart(productId);
    });
  });

  // =========================
  // Update Quantity
  // =========================

  document.querySelectorAll(".update-quantity").forEach((button) => {
    button.addEventListener("click", function (event) {
      const productId = this.dataset.id;

      const action = this.dataset.action;

      const maxStock = parseInt(this.dataset.max) || 999;

      updateQuantity(event, productId, action, maxStock, this);
    });
  });

  // =========================
  // Remove From Cart
  // =========================

  document.querySelectorAll(".remove-from-cart").forEach((button) => {
    button.addEventListener("click", function () {
      if (confirm("Are you sure you want to remove this item?")) {
        const productId = this.dataset.id;

        removeFromCart(productId);
      }
    });
  });
});

// =========================
// Add To Cart Function
// =========================

function addToCart(productId) {
  fetch("api/cart_actions.php", {
    method: "POST",

    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },

    body: `action=add&product_id=${productId}`,
  })
    .then((response) => response.json())

    .then((data) => {
      if (data.success) {
        showNotification("Product added to cart!", "success");

        updateCartCount();
      } else {
        if (data.message === "not_logged_in") {
          window.location.href =
            "register.php?redirect=" +
            encodeURIComponent(window.location.pathname);
        } else {
          showNotification(data.message || "Failed to add product", "error");
        }
      }
    })

    .catch((error) => {
      console.error("Error:", error);

      showNotification("An error occurred", "error");
    });
}

// =========================
// Update Quantity Function
// =========================

function updateQuantity(event, productId, action, maxStock, button) {
  const quantityInput = event.target

    .closest(".input-group")

    .querySelector(".quantity-input");

  let currentQuantity = parseInt(quantityInput.value);

  if (action === "increase") {
    if (currentQuantity >= maxStock) {
      showNotification("Maximum stock reached", "warning");

      return;
    }

    currentQuantity++;
  } else if (action === "decrease") {
    if (currentQuantity <= 1) {
      showNotification("Minimum quantity is 1", "warning");

      return;
    }

    currentQuantity--;
  }

  // Disable button during request

  button.disabled = true;

  button.style.opacity = "0.6";

  fetch("api/cart_actions.php", {
    method: "POST",

    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },

    body: `action=update&product_id=${productId}&quantity=${currentQuantity}`,
  })
    .then((response) => response.json())

    .then((data) => {
      if (data.success) {
        const item = document

          .querySelector(`[data-id="${productId}"]`)

          .closest(".cart-item");

        item.style.transition = ".4s";

        item.style.opacity = "0";

        item.style.transform = "translateX(50px)";

        setTimeout(() => {
          location.reload();
        }, 400);
      } else {
        button.disabled = false;

        button.style.opacity = "1";

        showNotification(
          data.message || "Failed to update quantity",

          "error",
        );
      }
    })

    .catch((error) => {
      console.error("Error:", error);

      button.disabled = false;

      button.style.opacity = "1";

      showNotification(
        "An error occurred",

        "error",
      );
    });
}
// =========================
// Remove From Cart Function
// =========================

function removeFromCart(productId) {
  fetch("api/cart_actions.php", {
    method: "POST",

    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },

    body: `action=remove&product_id=${productId}`,
  })
    .then((response) => response.json())

    .then((data) => {
      if (data.success) {
        location.reload();
      } else {
        showNotification(
          data.message || "Failed to remove product",

          "error",
        );
      }
    })

    .catch((error) => {
      console.error("Error:", error);

      showNotification(
        "An error occurred",

        "error",
      );
    });
}

// =========================
// Update Cart Count
// =========================

function updateCartCount() {
  fetch("api/cart_count.php")
    .then((response) => response.json())

    .then((data) => {
      const badge = document.querySelector(".badge.rounded-pill");

      if (badge) {
        badge.textContent = data.count;
      } else if (data.count > 0) {
        const cartLink = document.querySelector('a[href="cart.php"]');

        if (cartLink) {
          const newBadge = document.createElement("span");

          newBadge.className =
            "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger";

          newBadge.textContent = data.count;

          cartLink.querySelector("i").parentElement.appendChild(newBadge);
        }
      }
    })

    .catch((error) => {
      console.error("Error updating cart count:", error);
    });
}

// =========================
// Notification System
// =========================

function showNotification(message, type = "info") {
  const notification = document.createElement("div");

  notification.className = `alert alert-${
    type === "error"
      ? "danger"
      : type === "warning"
        ? "warning"
        : type === "success"
          ? "success"
          : "info"
  } notification-toast`;

  notification.style.cssText = `

    position: fixed;

    top: 20px;

    right: 20px;

    z-index: 9999;

    min-width: 250px;

    box-shadow: 0 4px 12px rgba(0,0,0,0.15);

    animation: slideIn 0.3s ease-out;

  `;

  const icon =
    type === "success"
      ? "check-circle"
      : type === "error"
        ? "exclamation-circle"
        : type === "warning"
          ? "exclamation-triangle"
          : "info-circle";

  notification.innerHTML = `

    <i class="fas fa-${icon} me-2"></i>

    ${message}

  `;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.animation = "slideOut 0.3s ease-out";

    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}

// =========================
// Notification Animations
// =========================

const style = document.createElement("style");

style.textContent = `

@keyframes slideIn {

  from {

    transform: translateX(400px);

    opacity:0;

  }


  to {

    transform: translateX(0);

    opacity:1;

  }

}



@keyframes slideOut {

  from {

    transform: translateX(0);

    opacity:1;

  }


  to {

    transform: translateX(400px);

    opacity:0;

  }

}



.notification-toast {

  animation: slideIn 0.3s ease-out;

}

`;

document.head.appendChild(style);

// =========================
// Form Validation
// =========================

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  return re.test(email);
}

function validatePhone(phone) {
  const re = /^[0-9]{10,15}$/;

  return re.test(phone.replace(/[\s-]/g, ""));
}

// =========================
// Smooth Scroll
// =========================

document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();

    const target = document.querySelector(this.getAttribute("href"));

    if (target) {
      target.scrollIntoView({
        behavior: "smooth",

        block: "start",
      });
    }
  });
});
