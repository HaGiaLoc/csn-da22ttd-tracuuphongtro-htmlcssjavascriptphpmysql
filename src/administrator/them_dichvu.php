<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenDichVu = $_POST['tenDichVu'];
    $moTaDichVu = $_POST['moTaDichVu'];
    $donVi = $_POST['donVi'];

    // Tạo mã dịch vụ mới
    $sql = "SELECT MAX(CAST(SUBSTRING(maDichVu, 3) AS UNSIGNED)) as max_id FROM dichvu";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $nextId = str_pad(($row['max_id'] ?? 0) + 1, 5, '0', STR_PAD_LEFT);
    $maDichVu = 'DV' . $nextId;

    // Thêm dịch vụ mới
    $sql = "INSERT INTO dichvu (maDichVu, tenDichVu, moTaDichVu, donVi) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $maDichVu, $tenDichVu, $moTaDichVu, $donVi);

    if ($stmt->execute()) {
        echo "<script>alert('Thêm dịch vụ thành công!'); window.location.href='admin_dashboard.php?page=danhsach_dichvu';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi thêm dịch vụ!'); window.location.href='admin_dashboard.php?page=them_dichvu';</script>";
    }
}
?>

<div class="product-form">
    <h2>Thêm dịch vụ mới</h2>
    <form method="POST">
        <div class="form-group">
            <label for="tenDichVu">Tên dịch vụ:</label>
            <input type="text" name="tenDichVu" required>
        </div>
        
        <div class="form-group">
            <label for="moTaDichVu">Mô tả dịch vụ:</label>
            <textarea name="moTaDichVu" rows="4" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="donVi">Đơn vị:</label>
            <input type="text" name="donVi" required placeholder="Ví dụ: kW, m³, tháng">
        </div>

        <button type="submit" class="add-new-btn">
            <i class="fas fa-plus"></i> Thêm dịch vụ
        </button>
    </form>
</div> 