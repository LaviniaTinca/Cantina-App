var emailInput = document.getElementById("add-email");
var passwordInput = document.getElementById("add-password");
var confirmPasswordInput = document.getElementById("add-confirm-password");

emailInput.addEventListener("input", validateEmail);
passwordInput.addEventListener("input", validatePassword);
confirmPasswordInput.addEventListener("input", validateConfirmPassword);

function validateEmail() {
  var email = emailInput.value;
  var emailError = document.getElementById("emailError");
  var emailRegex = /^\S+@\S+\.\S+$/;

  if (!emailRegex.test(email)) {
    emailError.innerHTML = "Adresa de e-mail invalidă";
    emailInput.classList.remove("valid");
    emailInput.classList.add("error");
  } else {
    emailError.innerHTML = "";
    emailInput.classList.remove("error");
    emailInput.classList.add("valid");
  }
}

function validatePassword() {
  var password = passwordInput.value;
  var passwordError = document.getElementById("passwordError");
  var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/;

  if (!passwordRegex.test(password)) {
    passwordError.innerHTML = "Parolă invalidă (minim 6 caractere, cel puțin o litera mare, o literă mică și o cifră!)";
    passwordInput.classList.remove("valid");
    passwordInput.classList.add("error");
  } else {
    passwordError.innerHTML = "";
    passwordInput.classList.remove("error");
    passwordInput.classList.add("valid");
  }
}

function validateConfirmPassword() {
  var password = confirmPasswordInput.value;
  var passwordError = document.getElementById("confirmPasswordError");
  var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/;

  if (!passwordRegex.test(password)) {
    passwordError.innerHTML = "Parolă invalidă (minim 6 caractere, cel puțin o litera mare, o literă mică și o cifră!)";
    confirmPasswordInput.classList.remove("valid");
    confirmPasswordInput.classList.add("error");
  } else if (password !== passwordInput.value) {
    passwordError.innerHTML = "Parolele nu corespund!";
    confirmPasswordInput.classList.remove("valid");
    confirmPasswordInput.classList.add("error");
  } else {
    passwordError.innerHTML = "";
    confirmPasswordInput.classList.remove("error");
    confirmPasswordInput.classList.add("valid");
  }
}

function togglePassword() {
    var passwordInput = document.getElementById("add-password");
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
    } else {
      passwordInput.type = "password";
    }
  }

//CORRECT TOGGLE -UNIQUE ID
function togglePasswordVisibility(inputId, buttonId) {
    var passwordInput = document.getElementById(inputId);
    var showPasswordBtn = document.getElementById(buttonId);
  
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      showPasswordBtn.textContent = "Hide";
    } else {
      passwordInput.type = "password";
    //   showPasswordBtn.textContent = "Show";
    showPasswordBtn.innerHTML = "<box-icon name='low-vision'></box-icon>";

    }
  }
  
  
