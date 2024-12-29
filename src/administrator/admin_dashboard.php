<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

require_once '../website/db_connection.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản trị Phòng trọ</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <h2>Menu Quản trị</h2>
            <nav>
                <ul>
                    <li>
                        <a href="admin_dashboard.php?page=trangchu" class="<?php echo isset($_GET['page']) && $_GET['page'] == 'trangchu' ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i>
                            <span>Trang chủ</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin_dashboard.php" class="<?php echo !isset($_GET['page']) || $_GET['page'] == 'danhsach_phongtro' ? 'active' : ''; ?>">
                            <i class="fas fa-list"></i>
                            <span>Quản lý phòng trọ</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin_dashboard.php?page=danhsach_nguoidung" class="<?php echo isset($_GET['page']) && $_GET['page'] == 'danhsach_nguoidung' ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i>
                            <span>Quản lý người dùng</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin_dashboard.php?page=danhsach_dichvu" class="<?php echo isset($_GET['page']) && $_GET['page'] == 'danhsach_dichvu' ? 'active' : ''; ?>">
                            <i class="fas fa-concierge-bell"></i>
                            <span>Quản lý dịch vụ</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="admin-content">
            <?php
            $page = $_GET['page'] ?? 'danhsach_phongtro';
            
            switch($page) {
                case 'trangchu':
                    include 'trangchu_admin.php';
                    break;
                case 'them_phongtro':
                    include 'them_phongtro.php';
                    break;
                case 'them_nguoidung':
                    include 'them_nguoidung.php';
                    break;
                case 'them_dichvu':
                    include 'them_dichvu.php';
                    break;
                case 'sua_phongtro':
                    if (isset($_GET['id'])) {
                        include 'sua_phongtro.php';
                    } else {
                        include 'danhsach_phongtro.php';
                    }
                    break;
                case 'xoa_phongtro':
                    if (isset($_GET['id'])) {
                        include 'xoa_phongtro.php';
                    } else {
                        include 'danhsach_phongtro.php';
                    }
                    break;
                case 'danhsach_nguoidung':
                    include 'danhsach_nguoidung.php';
                    break;
                case 'sua_nguoidung':
                    if (isset($_GET['id'])) {
                        include 'sua_nguoidung.php';
                    } else {
                        include 'danhsach_nguoidung.php';
                    }
                    break;
                case 'xoa_nguoidung':
                    if (isset($_GET['id'])) {
                        include 'xoa_nguoidung.php';
                    } else {
                        include 'danhsach_nguoidung.php';
                    }
                    break;
                case 'danhsach_dichvu':
                    include 'danhsach_dichvu.php';
                    break;
                default:
                    include 'danhsach_phongtro.php';
                    break;
            }
            ?>
        </div>
    </div>
</body>
</html> 