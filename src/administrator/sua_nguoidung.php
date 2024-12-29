<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

$maNguoiDung = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenNguoiDung = $_POST['tenNguoiDung'];
    $tenDangNhap = $_POST['tenDangNhap'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];
    $matKhau = $_POST['matKhau'];
    
    // Kiểm tra xem người dùng hiện tại có phải là admin không
    $checkSql = "SELECT vaiTro FROM nguoiDung WHERE maNguoiDung = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $maNguoiDung);
    $checkStmt->execute();
    $currentUser = $checkStmt->get_result()->fetch_assoc();
    
    // Nếu là admin, giữ nguyên vai trò admin
    if ($currentUser['vaiTro'] === 'admin') {
        $vaiTro = 'admin';
    } else {
        $vaiTro = $_POST['vaiTro'];
    }

    // Cập nhật thông tin người dùng
    if (!empty($matKhau)) {
        $sql = "UPDATE nguoiDung SET 
                tenNguoiDung = ?,
                tenDangNhap = ?,
                emailNguoiDung = ?,
                sdtNguoiDung = ?,
                vaiTro = ?,
                matKhau = ?
                WHERE maNguoiDung = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $tenNguoiDung, $tenDangNhap, $email, $sdt, $vaiTro, $matKhau, $maNguoiDung);
    } else {
        $sql = "UPDATE nguoiDung SET 
                tenNguoiDung = ?,
                tenDangNhap = ?,
                emailNguoiDung = ?,
                sdtNguoiDung = ?,
                vaiTro = ?
                WHERE maNguoiDung = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $tenNguoiDung, $tenDangNhap, $email, $sdt, $vaiTro, $maNguoiDung);
    }
            
    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật thông tin người dùng thành công!'); window.location.href='admin_dashboard.php?page=danhsach_nguoidung';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi cập nhật thông tin!'); window.location.href='admin_dashboard.php?page=danhsach_nguoidung';</script>";
    }
}

// Lấy thông tin người dùng
$sql = "SELECT * FROM nguoiDung WHERE maNguoiDung = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maNguoiDung);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: admin_dashboard.php?page=danhsach_nguoidung');
    exit();
}
?>

<div class="product-form">
    <h2>Sửa thông tin người dùng</h2>
    <form method="POST">
        <div class="form-group">
            <label for="tenNguoiDung">Tên người dùng:</label>
            <input type="text" name="tenNguoiDung" value="<?php echo $user['tenNguoiDung']; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="tenDangNhap">Tên đăng nhập:</label>
            <input type="text" name="tenDangNhap" value="<?php echo $user['tenDangNhap']; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="matKhau">Mật khẩu:</label>
            <input type="password" name="matKhau" value="<?php echo $user['matKhau']; ?>">
            <div class="password-toggle">
                <input type="checkbox" onclick="togglePassword()"> Hiện mật khẩu
            </div>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo $user['emailNguoiDung']; ?>">
        </div>
        
        <div class="form-group">
            <label for="sdt">Số điện thoại:</label>
            <input type="tel" name="sdt" value="<?php echo $user['sdtNguoiDung']; ?>">
        </div>
        
        <div class="form-group">
            <label for="vaiTro">Vai trò:</label>
            <?php if ($user['vaiTro'] === 'admin'): ?>
                <input type="text" value="Quản trị viên" disabled>
                <input type="hidden" name="vaiTro" value="admin">
            <?php else: ?>
                <select name="vaiTro">
                    <option value="user" <?php echo $user['vaiTro'] === 'user' ? 'selected' : ''; ?>>Người dùng</option>
                    <option value="admin" <?php echo $user['vaiTro'] === 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                </select>
            <?php endif; ?>
        </div>

        <button type="submit" class="edit-btn">
            <i class="fas fa-save"></i> Lưu thay đổi
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
</div> 