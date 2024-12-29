<?php
// Hàm lấy danh sách phòng trọ
function getPhongTro($conn) {
    $sql = "SELECT * FROM phongtro";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Hàm lấy thông tin phòng trọ theo ID
function getPhongTroById($conn, $id) {
    $sql = "SELECT * FROM phongtro WHERE maPhongTro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Hàm cập nhật thông tin phòng trọ
function updatePhongTro($conn, $id, $diaChi, $giaPhong, $dienTich) {
    $sql = "UPDATE phongtro SET diaChi = ?, giaPhong = ?, dienTich = ? WHERE maPhongTro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siii', $diaChi, $giaPhong, $dienTich, $id);
    $stmt->execute();
}

// Hàm xóa phòng trọ
function deletePhongTro($conn, $id) {
    $sql = "DELETE FROM phongtro WHERE maPhongTro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
?> 