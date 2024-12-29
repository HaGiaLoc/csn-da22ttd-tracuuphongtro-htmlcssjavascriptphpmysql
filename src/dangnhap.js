// Hàm mở modal đăng nhập
function openModal() {
    document.getElementById("login-modal").style.display = "block";
}

// Hàm đóng modal đăng nhập
function closeModal() {
    document.getElementById("login-modal").style.display = "none";
}

// Sự kiện DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("login-modal");
    const modalContent = modal.querySelector(".modal-content");
    const logoutBtn = document.getElementById("logout-btn");

    // Sự kiện click ngoài modal
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Sự kiện click trong modal
    modalContent.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    // Sự kiện click nút đăng xuất
    logoutBtn.addEventListener('click', logout);
    
    // Kiểm tra trạng thái đăng nhập
    checkLoginStatus();

    // Sự kiện submit form đăng nhập
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('login-username').value;
        const password = document.getElementById('login-password').value;

        const formData = new FormData();
        formData.append('username', username);
        formData.append('password', password);
        formData.append('action', 'login');

        // Gửi yêu cầu đăng nhập
        fetch('login_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                document.getElementById('error-msg').textContent = data.message;
                document.getElementById('error-msg').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});

// Sự kiện onload
window.onload = function() {
    const loginForm = document.getElementById("login-form");
    const errorDisplay = document.getElementById("error-msg");

    if (!errorDisplay) {
        console.error('Phần tử error-msg không tồn tại.');
        return;
    }

    // Sự kiện submit form đăng nhập
    loginForm.addEventListener("submit", async function(event) {
        event.preventDefault();
        
        const username = document.getElementById("login-username").value;
        const password = document.getElementById("login-password").value;

        if (!username || !password) {
            showError("Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu");
            return;
        }
        
        const formData = { username, password };

        try {
            const response = await fetch('dangnhap.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            showError(data.message, data.success);

            if (data.success) {
                loginForm.reset();
                showUsername(data.user);
                closeModal();
            }
        } catch (error) {
            console.error('Lỗi đăng nhập:', error);
            showError("Có lỗi xảy ra khi đăng nhập");
        }
    });
};

// Hàm hiển thị lỗi
function showError(message, isSuccess = false) {
    const errorDisplay = document.getElementById("error-msg");
    errorDisplay.innerHTML = message;
    errorDisplay.style.display = "block";
    errorDisplay.style.color = isSuccess ? "#28a745" : "#dc3545";
}

// Hàm hiển thị tên người dùng
function showUsername(userData) {
    const userInfo = document.getElementById("user-info");
    userInfo.innerHTML = `${userData.tenNguoiDung}`;
    userInfo.style.display = "block";

    document.getElementById("login-btn").style.display = "none";
    document.getElementById("logout-btn").style.display = "flex";
}

// Hàm đăng xuất
async function logout() {
    try {
        const response = await fetch('dangnhap.php?action=logout');
        const data = await response.json();
        if (data.success) {
            location.reload(); // Chỉ reload khi đăng xuất
        }
    } catch (error) {
        console.error('Lỗi đăng xuất:', error);
    }
}

// Hàm kiểm tra trạng thái đăng nhập
async function checkLoginStatus() {
    try {
        const response = await fetch('dangnhap.php?action=check');
        const data = await response.json();
        if (data.isLoggedIn) {
            showUsername(data.user);
        }
    } catch (error) {
        console.error('Lỗi kiểm tra đăng nhập:', error);
    }
}