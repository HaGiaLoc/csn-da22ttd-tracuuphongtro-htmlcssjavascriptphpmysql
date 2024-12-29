<?php
session_start();
require_once '../website/db_connection.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$image = $data['image'] ?? '';
$maPhongTro = $data['maPhongTro'] ?? '';

if (empty($image) || empty($maPhongTro)) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

try {
    // Lấy thông tin ảnh hiện tại
    $sql = "SELECT hinhAnh FROM hinhanhphong WHERE maPhongTro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maPhongTro);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $current_images = explode("\n", $row['hinhAnh']);
        
        // Loại bỏ ảnh cần xóa
        $new_images = array_filter($current_images, function($img) use ($image) {
            return trim($img) !== trim($image);
        });
        
        // Cập nhật danh sách ảnh mới
        $new_images_str = implode("\n", $new_images);
        
        // Cập nhật bảng hinhanhphong
        $sql = "UPDATE hinhanhphong SET hinhAnh = ? WHERE maPhongTro = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_images_str, $maPhongTro);
        $stmt->execute();
        
        // Kiểm tra nếu không còn ảnh nào thì xóa ảnh đại diện
        if (empty($new_images)) {
            $sql = "UPDATE hinhanhphong SET anhDaiDien = NULL WHERE maPhongTro = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $maPhongTro);
            $stmt->execute();
        }
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No images found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 