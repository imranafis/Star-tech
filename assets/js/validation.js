// Client-side validation functions

function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function validatePassword(password) {
  return password.length >= 6;
}

function validateUsername(username) {
  return username.length >= 3;
}

function validateRequired(value) {
  return value.trim() !== "";
}

function validatePrice(price) {
  return !isNaN(price) && parseFloat(price) > 0;
}

function validateImage(file) {
  const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
  const maxSize = 5 * 1024 * 1024; // 5MB

  if (!file) return { valid: false, message: "Please select an image" };

  if (!allowedTypes.includes(file.type)) {
    return {
      valid: false,
      message: "Only JPG, PNG, and GIF images are allowed",
    };
  }

  if (file.size > maxSize) {
    return { valid: false, message: "Image size must be less than 5MB" };
  }

  return { valid: true };
}

function showError(elementId, message) {
  const errorElement = document.getElementById(elementId);
  if (errorElement) {
    errorElement.textContent = message;
  }
}

function clearErrors() {
  document.querySelectorAll(".error").forEach((el) => (el.textContent = ""));
}

function showMessage(elementId, message, type = "success") {
  const messageElement = document.getElementById(elementId);
  if (messageElement) {
    messageElement.className = `message ${type}`;
    messageElement.textContent = message;
  }
}
