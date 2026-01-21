// Main JavaScript utilities

// AJAX helper function
async function makeRequest(url, method = "GET", data = null) {
  try {
    const options = {
      method: method,
      headers: {},
    };

    if (data) {
      if (data instanceof FormData) {
        options.body = data;
      } else {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(data);
      }
    }

    const response = await fetch(url, options);
    return await response.json();
  } catch (error) {
    console.error("Request error:", error);
    return { success: false, message: "Network error occurred" };
  }
}

// Format price
function formatPrice(price) {
  return "$" + parseFloat(price).toFixed(2);
}

// Show loading indicator
function showLoading(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.innerHTML = '<div class="loading">Loading...</div>';
  }
}

// Hide loading indicator
function hideLoading(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.innerHTML = "";
  }
}

// Smooth scroll to top
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
}

// Debounce function for search/filter
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Initialize tooltips or other interactive elements
document.addEventListener("DOMContentLoaded", function () {
  // Add any global initialization here
  console.log("Star Tech loaded successfully");
});
