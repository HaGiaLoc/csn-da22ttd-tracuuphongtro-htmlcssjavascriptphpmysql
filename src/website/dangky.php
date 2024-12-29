<?php
require_once 'db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra dữ liệu bắt buộc
    if (empty($_POST['fullname']) || empty($_POST['username']) || empty($_POST['password'])) {
        $_SESSION['message'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
        $_SESSION['message_type'] = 'error';
        header('Location: index.php?show_modal=1');
        exit();
    }

    // Kiểm tra tên đăng nhập đã tồn tại
    $checkSql = "SELECT * FROM nguoiDung WHERE tenDangNhap = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $_POST['username']);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        $_SESSION['message'] = 'Tên đăng nhập đã tồn tại';
        $_SESSION['message_type'] = 'error';
        header('Location: index.php?show_modal=1');
        exit();
    }

    // Tạo mã người dùng mới
    $sql = "SELECT MAX(CAST(SUBSTRING(maNguoiDung, 3) AS UNSIGNED)) as max_id FROM nguoiDung WHERE maNguoiDung LIKE 'ND%'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $nextId = str_pad(($row['max_id'] ?? 0) + 1, 5, '0', STR_PAD_LEFT);
    $maNguoiDung = 'ND' . $nextId;

    // Thêm người dùng mới
    $sql = "INSERT INTO nguoiDung (maNguoiDung, tenNguoiDung, matKhau, tenDangNhap, sdtNguoiDung, emailNguoiDung) 
            VALUES (?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", 
        $maNguoiDung,
        $_POST['fullname'],
        $_POST['password'],
        $_POST['username'],
        $_POST['phone'],
        $_POST['email']
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Đăng ký thành công';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Lỗi khi đăng ký: ' . $conn->error;
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: index.php?show_modal=1');
    exit();
}
?> 