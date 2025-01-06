<?php
session_start();
require_once '../website/db_connection.php';

// Kiểm tra quyền truy cập
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

$maDichVu = $_GET['id'] ?? null; // Lấy mã dịch vụ từ tham số GET

if ($maDichVu) {
    // Xóa tất cả các bản ghi liên quan trong bảng khuTro_dichVu
    $sql_delete_related = "DELETE FROM khuTro_dichVu WHERE maDichVu = ?";
    $stmt_delete_related = $conn->prepare($sql_delete_related);
    $stmt_delete_related->bind_param("s", $maDichVu);
    $stmt_delete_related->execute();

    // Xóa dịch vụ
    $sql = "DELETE FROM dichvu WHERE maDichVu = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maDichVu);
    
    if ($stmt->execute()) {
        echo "<script>alert('Xóa dịch vụ thành công!'); window.location.href='admin_dashboard.php?page=danhsach_dichvu';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi xóa dịch vụ!'); window.location.href='admin_dashboard.php?page=danhsach_dichvu';</script>";
    }
} else {
    echo "<script>alert('Không có mã dịch vụ, vui lòng kiểm tra lại!');</script>";
}
?> 