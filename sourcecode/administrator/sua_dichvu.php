<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

$maDichVu = $_GET['id'] ?? null;
if (!$maDichVu) {
    header('Location: admin_dashboard.php?page=danhsach_dichvu');
    exit();
}

// Xử lý cập nhật dịch vụ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenDichVu = $_POST['tenDichVu'];
    $moTaDichVu = $_POST['moTaDichVu'];
    $donVi = $_POST['donVi'];

    // Cập nhật thông tin dịch vụ
    $sql = "UPDATE dichvu SET tenDichVu = ?, moTaDichVu = ?, donVi = ? WHERE maDichVu = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $tenDichVu, $moTaDichVu, $donVi, $maDichVu);

    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật dịch vụ thành công!'); window.location.href='admin_dashboard.php?page=danhsach_dichvu';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi cập nhật dịch vụ!'); window.location.href='admin_dashboard.php?page=sua_dichvu&id=" . $maDichVu . "';</script>";
    }
}

// Lấy thông tin dịch vụ hiện tại
$sql = "SELECT * FROM dichvu WHERE maDichVu = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maDichVu);
$stmt->execute();
$dichvu = $stmt->get_result()->fetch_assoc();

if (!$dichvu) {
    header('Location: admin_dashboard.php?page=danhsach_dichvu');
    exit();
}
?>

<div class="product-form">
    <h2>Sửa thông tin dịch vụ</h2>
    <form method="POST">
        <div class="form-group">
            <label for="maDichVu">Mã dịch vụ:</label>
            <input type="text" name="maDichVu" value="<?php echo $dichvu['maDichVu']; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="tenDichVu">Tên dịch vụ:</label>
            <input type="text" name="tenDichVu" value="<?php echo $dichvu['tenDichVu']; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="moTaDichVu">Mô tả dịch vụ:</label>
            <textarea name="moTaDichVu" rows="4" required><?php echo $dichvu['moTaDichVu']; ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="donVi">Đơn vị:</label>
            <input type="text" name="donVi" value="<?php echo $dichvu['donVi']; ?>" required placeholder="Ví dụ: kW, m³, tháng">
        </div>

        <button type="submit" class="edit-btn">
            <i class="fas fa-save"></i> Lưu thay đổi
        </button>
    </form>
</div> 