<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenNguoiDung = $_POST['tenNguoiDung'];
    $tenDangNhap = $_POST['tenDangNhap'];
    $matKhau = $_POST['matKhau'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];
    $vaiTro = $_POST['vaiTro'];

    // Kiểm tra tên đăng nhập đã tồn tại
    $checkSql = "SELECT * FROM nguoiDung WHERE tenDangNhap = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $tenDangNhap);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        echo "<script>alert('Tên đăng nhập đã tồn tại!'); window.location.href='admin_dashboard.php?page=them_nguoidung';</script>";
        exit();
    }

    // Tạo mã người dùng mới
    $sql = "SELECT MAX(CAST(SUBSTRING(maNguoiDung, 3) AS UNSIGNED)) as max_id FROM nguoiDung";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $nextId = str_pad(($row['max_id'] ?? 0) + 1, 5, '0', STR_PAD_LEFT);
    $maNguoiDung = 'ND' . $nextId;

    // Thêm người dùng mới
    $sql = "INSERT INTO nguoiDung (maNguoiDung, tenNguoiDung, tenDangNhap, matKhau, emailNguoiDung, sdtNguoiDung, vaiTro) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $maNguoiDung, $tenNguoiDung, $tenDangNhap, $matKhau, $email, $sdt, $vaiTro);

    if ($stmt->execute()) {
        echo "<script>alert('Thêm người dùng thành công!'); window.location.href='admin_dashboard.php?page=danhsach_nguoidung';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi thêm người dùng!'); window.location.href='admin_dashboard.php?page=them_nguoidung';</script>";
    }
}
?>

<div class="product-form">
    <h2>Thêm người dùng mới</h2>
    <form method="POST">
        <div class="form-group">
            <label for="tenNguoiDung">Tên người dùng:</label>
            <input type="text" name="tenNguoiDung" required>
        </div>
        
        <div class="form-group">
            <label for="tenDangNhap">Tên đăng nhập:</label>
            <input type="text" name="tenDangNhap" required>
        </div>
        
        <div class="form-group">
            <label for="matKhau">Mật khẩu:</label>
            <input type="password" name="matKhau" required>
            <div class="password-toggle">
                <input type="checkbox" onclick="togglePassword()"> Hiện mật khẩu
            </div>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email">
        </div>
        
        <div class="form-group">
            <label for="sdt">Số điện thoại:</label>
            <input type="tel" name="sdt">
        </div>
        
        <div class="form-group">
            <label for="vaiTro">Vai trò:</label>
            <select name="vaiTro" required>
                <option value="user">Người dùng</option>
                <option value="admin">Quản trị viên</option>
            </select>
        </div>

        <button type="submit" class="add-new-btn">
            <i class="fas fa-plus"></i> Thêm người dùng
        </button>
    </form>
</div>

<script>
function togglePassword() {
    var x = document.getElementsByName("matKhau")[0];
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}
</script> 