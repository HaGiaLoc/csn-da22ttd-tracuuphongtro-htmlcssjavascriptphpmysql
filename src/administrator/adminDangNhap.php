<?php
session_start();
require_once '../website/db_connection.php';

// Kiểm tra nếu đã đăng nhập thì chuyển đến trang quản trị
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: admin_dashboard.php');
    exit();
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Kiểm tra trong database
    $sql = "SELECT * FROM nguoiDung WHERE tenDangNhap = ? AND matKhau = ? AND vaiTro = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin'] = true;
        $_SESSION['admin_info'] = array(
            'maNguoiDung' => $admin['maNguoiDung'],
            'tenNguoiDung' => $admin['tenNguoiDung']
        );
        // Chuyển hướng đến trang chủ quản trị
        header('Location: admin_dashboard.php?page=trangchu');
        exit();
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng, hoặc tài khoản không có quyền quản trị!';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập quản trị</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-form">
            <h2>Đăng nhập quản trị</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Tên đăng nhập:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-btn" style="width: auto; padding: 8px 30px; margin: 0 auto; display: block;">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>
            <div class="back-to-site">
                <a href="../website/index.php">
                    <i class="fas fa-arrow-left"></i> Quay lại trang web
                </a>
            </div>
        </div>
    </div>
</body>
</html> 