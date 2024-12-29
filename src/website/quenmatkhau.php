<?php
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Kiểm tra mật khẩu mới và xác nhận mật khẩu
    if ($new_password !== $confirm_password) {
        $error = "Mật khẩu mới và xác nhận mật khẩu không khớp!";
    } else {
        // Kiểm tra tên đăng nhập và mật khẩu cũ
        $sql = "SELECT * FROM nguoidung WHERE tenDangNhap = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Cập nhật mật khẩu mới
            $update_sql = "UPDATE nguoidung SET matKhau = ? WHERE tenDangNhap = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $new_password, $username);
            
            if ($update_stmt->execute()) {
                $success = "Đổi mật khẩu thành công!";
            } else {
                $error = "Có lỗi xảy ra khi cập nhật mật khẩu!";
            }
        } else {
            $error = "Tên đăng nhập không tồn tại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt Lại Mật Khẩu</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="main-container">
        <div class="forgot-password-container">
            <h2>Đặt Lại Mật Khẩu</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST" class="forgot-password-form">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Mật khẩu mới</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Nhập lại mật khẩu mới</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="button-container">
                    <button type="submit">Đặt lại mật khẩu</button>
                    <a href="index.php" class="cancel-button">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html> 