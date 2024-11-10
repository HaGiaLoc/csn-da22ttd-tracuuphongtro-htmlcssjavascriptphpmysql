function openModal()
{
    document.getElementById("login-modal").style.display="block";
}

function closeModal() {
    document.getElementById("login-modal").style.display="none";
}

window.onclick = function(event) {
    if (event.target == document.getElementById("login-modal")) {
        closeModal();
    }
}

function registerUser(username, password) {
    localStorage.setItem(username, password);
}

function checkCredentials(username, password) {
    return localStorage.getItem(username) === password;
}

function showUsername(username) {
    const userInfo = document.getElementById("user-info");
    userInfo.innerHTML = "Xin chào, <br>" + username;
    userInfo.style.display = "block";

    const loginBtn = document.getElementById("login-btn");
    loginBtn.style.display = "none";

    closeModal();

    const logoutBtn = document.getElementById("logout-btn");
    logoutBtn.style.display = "flex";
}

function logout() {
    localStorage.removeItem("loggedInUser");
    location.reload();
}

window.onload = function() {
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");
    const errorDisplay = document.getElementById("error-msg");

    loginForm.addEventListener("submit", function(event) {
        event.preventDefault();
        const username = document.getElementById("login-username").value;
        const password = document.getElementById("login-password").value;

        if (checkCredentials(username, password)) {
            localStorage.setItem("loggedInUser", username);
            showUsername(username);
        } else {
            errorDisplay.innerHTML = "Sai tên người dùng hoặc mật khẩu.";
            errorDisplay.style.display = "block";
        }
    });

    registerForm.addEventListener("submit", function(event) {
        event.preventDefault();
        const regUsername = document.getElementById("register-email").value;
        const regPassword = document.getElementById("register-password").value;

        registerUser(regUsername, regPassword);
        document.getElementById("register-form").reset();
        errorDisplay.innerHTML = "Đăng ký thành công!";
    });

    const loggedInUser = localStorage.getItem("loggedInUser");
    if (loggedInUser) {
        showUsername(loggedInUser);
    }

    const logoutBtn = document.getElementById("logout-btn");
    logoutBtn.addEventListener("click", logout);
};