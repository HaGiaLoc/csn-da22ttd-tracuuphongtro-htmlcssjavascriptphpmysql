<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

$maPhongTro = $_GET['id'] ?? null;

if ($maPhongTro) {
    // Xóa hình ảnh
    $sql = "DELETE FROM hinhanhphong WHERE maPhongTro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maPhongTro);
    $stmt->execute();
    
    // Xóa đánh giá
    $sql = "DELETE FROM danhgia WHERE maPhongTro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maPhongTro);
    $stmt->execute();
    
    // Xóa phòng trọ
    $sql = "DELETE FROM phongtro WHERE maPhongTro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maPhongTro);
    
    if ($stmt->execute()) {
        echo "<script>alert('Xóa phòng trọ thành công!'); window.location.href='admin_dashboard.php?page=danhsach_phongtro';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi xóa phòng trọ!'); window.location.href='admin_dashboard.php?page=danhsach_phongtro';</script>";
    }
} else {
    header('Location: admin_dashboard.php?page=danhsach_phongtro');
}
?> 