<?php
require_once 'db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Kiểm tra dữ liệu bắt buộc
    if (empty($data['username']) || empty($data['password'])) {
        $response = array(
            'success' => false,
            'message' => 'Vui lòng điền đầy đủ tên đăng nhập và mật khẩu'
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    $tenDangNhap = $data['username'];
    $matKhau = $data['password'];
    
    // Kiểm tra tài khoản có tồn tại
    $sql = "SELECT * FROM nguoiDung WHERE tenDangNhap = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tenDangNhap);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $response = array(
            'success' => false,
            'message' => 'Tài khoản không tồn tại'
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    // Kiểm tra đăng nhập và vai trò
    $sql = "SELECT maNguoiDung, tenNguoiDung, tenDangNhap, vaiTro 
            FROM nguoiDung 
            WHERE tenDangNhap = ? AND matKhau = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $tenDangNhap, $matKhau);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Kiểm tra vai trò
        if ($user['vaiTro'] !== 'user') {
            $response = array(
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
            );
        } else {
            $_SESSION['user'] = $user;
            $response = array(
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'user' => array(
                    'tenNguoiDung' => $user['tenNguoiDung']
                )
            );
        }
    } else {
        $response = array(
            'success' => false,
            'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Kiểm tra trạng thái đăng nhập
if (isset($_GET['action']) && $_GET['action'] == 'check') {
    $response = array(
        'isLoggedIn' => isset($_SESSION['user']),
        'user' => isset($_SESSION['user']) ? array(
            'tenNguoiDung' => $_SESSION['user']['tenNguoiDung']
        ) : null
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Xử lý đăng xuất
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    $response = array(
        'success' => true,
        'message' => 'Đăng xuất thành công'
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?> 