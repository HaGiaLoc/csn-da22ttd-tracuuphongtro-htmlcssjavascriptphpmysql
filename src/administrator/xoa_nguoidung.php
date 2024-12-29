<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

$maNguoiDung = $_GET['id'] ?? null;

if ($maNguoiDung) {
    // Kiểm tra xem người dùng có phải admin không
    $sql = "SELECT vaiTro FROM nguoiDung WHERE maNguoiDung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maNguoiDung);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user['vaiTro'] === 'admin') {
        echo "<script>alert('Không thể xóa tài khoản admin!'); window.location.href='admin_dashboard.php?page=danhsach_nguoidung';</script>";
        exit();
    }

    // Xóa đánh giá của người dùng
    $sql = "DELETE FROM danhgia WHERE maNguoiDung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maNguoiDung);
    $stmt->execute();
    
    // Xóa người dùng
    $sql = "DELETE FROM nguoiDung WHERE maNguoiDung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maNguoiDung);
    
    if ($stmt->execute()) {
        echo "<script>alert('Xóa người dùng thành công!'); window.location.href='admin_dashboard.php?page=danhsach_nguoidung';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi xóa người dùng!'); window.location.href='admin_dashboard.php?page=danhsach_nguoidung';</script>";
    }
} else {
    header('Location: admin_dashboard.php?page=danhsach_nguoidung');
}
?> 