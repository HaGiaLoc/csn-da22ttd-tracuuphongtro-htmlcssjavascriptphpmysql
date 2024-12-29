<?php
// Kiểm tra hằng số để tránh include nhiều lần
if (!defined('DB_LOADED')) {
    define('DB_LOADED', true);

    // Thông tin kết nối database
    $host = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $database = "phongtro_db"; 

    // Tạo kết nối
    try {
        $conn = new mysqli($host, $username, $password, $database);
        
        if ($conn->connect_error) {
            throw new Exception("Kết nối thất bại: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8");
        
    } catch (Exception $e) {
        die("Lỗi: " . $e->getMessage());
    }

    // Hàm đóng kết nối database
    function closeConnection($conn) {
        if ($conn) {
            $conn->close();
        }
    }

    // Hàm để thực thi truy vấn an toàn
    function executeQuery($conn, $sql, $params = []) {
        try {
            $stmt = $conn->prepare($sql);
            
            if ($stmt === false) {
                throw new Exception("Lỗi prepare statement: " . $conn->error);
            }
            
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            return $stmt;
            
        } catch (Exception $e) {
            die("Lỗi thực thi truy vấn: " . $e->getMessage());
        }
    }
    //pt.ngayDang,
    function getPhongTro($conn) {
        $sql = "SELECT 
            pt.maPhongTro,
            pt.ngayDang,
            pt.dienTich,
            pt.tinhTrang,
            lp.giaPhong,
            lp.moTaPhongTro,
            kt.diaChi,
            kt.chuTro,
            kt.sdtChuTro,
            hp.anhDaiDien
        FROM phongtro pt
        JOIN khutro kt ON pt.maKhuTro = kt.maKhuTro
        JOIN loaiphong lp ON pt.maLoaiPhong = lp.maLoaiPhong
        JOIN hinhanhphong hp ON pt.maPhongTro = hp.maPhongTro
        ORDER BY pt.maPhongTro ASC";
        
        $stmt = executeQuery($conn, $sql);
        $result = $stmt->get_result();
        
        $phongtroList = array();
        while($row = $result->fetch_assoc()) {
            // Chuyển đổi hình ảnh BLOB thành base64
            if ($row['anhDaiDien']) {
                $row['anhDaiDien'] = base64_encode($row['anhDaiDien']);
            }
            $phongtroList[] = $row;
        }
        
        return $phongtroList;
    }
}
?> 