<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

// Đếm số lượng phòng trọ
$sql_phongtro = "SELECT COUNT(*) as total FROM phongtro";
$result_phongtro = $conn->query($sql_phongtro);
$phongtro_count = $result_phongtro->fetch_assoc()['total'];

// Đếm số lượng người dùng
$sql_users = "SELECT COUNT(*) as total FROM nguoiDung WHERE vaiTro = 'user'";
$result_users = $conn->query($sql_users);
$users_count = $result_users->fetch_assoc()['total'];

// Đếm số lượng dịch vụ
$sql_services = "SELECT COUNT(*) as total FROM dichvu";
$result_services = $conn->query($sql_services);
$services_count = $result_services->fetch_assoc()['total'];

// Đếm số phòng trống
$sql_empty = "SELECT COUNT(*) as total FROM phongtro WHERE tinhTrang = 'empty'";
$result_empty = $conn->query($sql_empty);
$empty_count = $result_empty->fetch_assoc()['total'];

// Lấy 5 phòng trọ mới nhất
$sql_latest_rooms = "SELECT 
    pt.maPhongTro,
    pt.ngayDang,
    pt.dienTich,
    pt.tinhTrang,
    lp.giaPhong,
    kt.diaChi,
    kt.chuTro
FROM phongtro pt
JOIN khutro kt ON pt.maKhuTro = kt.maKhuTro
JOIN loaiphong lp ON pt.maLoaiPhong = lp.maLoaiPhong
ORDER BY pt.ngayDang DESC
LIMIT 5";
$result_latest_rooms = $conn->query($sql_latest_rooms);

// Lấy 5 người dùng mới nhất
$sql_latest_users = "SELECT 
    maNguoiDung,
    tenNguoiDung,
    tenDangNhap,
    emailNguoiDung,
    sdtNguoiDung,
    vaiTro
FROM nguoiDung
ORDER BY maNguoiDung DESC
LIMIT 5";
$result_latest_users = $conn->query($sql_latest_users);

// Lấy danh sách dịch vụ
$sql_services_list = "SELECT * FROM dichvu ORDER BY maDichVu DESC";
$result_services_list = $conn->query($sql_services_list);
?>

<div class="admin-dashboard">
    <h2>Tổng quan hệ thống</h2>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="stat-info">
                <h3>Phòng trọ</h3>
                <p><?php echo $phongtro_count; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>Người dùng</h3>
                <p><?php echo $users_count; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-concierge-bell"></i>
            </div>
            <div class="stat-info">
                <h3>Dịch vụ</h3>
                <p><?php echo $services_count; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-door-open"></i>
            </div>
            <div class="stat-info">
                <h3>Phòng trống</h3>
                <p><?php echo $empty_count; ?></p>
            </div>
        </div>
    </div>

    <div class="dashboard-tables">
        <!-- Bảng phòng trọ mới nhất -->
        <div class="dashboard-table">
            <div class="table-header">
                <h3>Phòng trọ mới nhất</h3>
                <a href="admin_dashboard.php?page=danhsach_phongtro" class="view-all">Xem tất cả</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Mã phòng</th>
                        <th>Địa chỉ</th>
                        <th>Diện tích</th>
                        <th>Giá phòng</th>
                        <th>Tình trạng</th>
                        <th>Ngày đăng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_latest_rooms->num_rows > 0): ?>
                        <?php while($room = $result_latest_rooms->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $room['maPhongTro']; ?></td>
                            <td><?php echo $room['diaChi']; ?></td>
                            <td><?php echo $room['dienTich']; ?> m²</td>
                            <td><?php echo number_format($room['giaPhong'], 0, ',', '.'); ?> VND</td>
                            <td><?php echo $room['tinhTrang'] ? 'Còn trống' : 'Đã cho thuê'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($room['ngayDang'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Không có phòng trọ nào</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Bảng người dùng mới nhất -->
        <div class="dashboard-table">
            <div class="table-header">
                <h3>Người dùng mới nhất</h3>
                <a href="admin_dashboard.php?page=danhsach_nguoidung" class="view-all">Xem tất cả</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Mã ND</th>
                        <th>Tên người dùng</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_latest_users->num_rows > 0): ?>
                        <?php while($user = $result_latest_users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['maNguoiDung']; ?></td>
                            <td><?php echo $user['tenNguoiDung']; ?></td>
                            <td><?php echo $user['tenDangNhap']; ?></td>
                            <td><?php echo $user['emailNguoiDung']; ?></td>
                            <td><?php echo $user['sdtNguoiDung']; ?></td>
                            <td>
                            <span class="role <?php echo $user['vaiTro']; ?>">
                                <?php echo $user['vaiTro'] === 'admin' ? 'Quản trị viên' : 'Người dùng'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Không có người dùng nào</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Bảng dịch vụ -->
        <div class="dashboard-table">
            <div class="table-header">
                <h3>Danh sách dịch vụ</h3>
                <a href="admin_dashboard.php?page=danhsach_dichvu" class="view-all">Xem tất cả</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Mã dịch vụ</th>
                        <th>Tên dịch vụ</th>
                        <th>Mô tả</th>
                        <th>Đơn vị</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_services_list->num_rows > 0): ?>
                        <?php while($service = $result_services_list->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $service['maDichVu']; ?></td>
                            <td><?php echo $service['tenDichVu']; ?></td>
                            <td><?php echo $service['moTaDichVu']; ?></td>
                            <td><?php echo $service['donVi']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">Không có dịch vụ nào</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div> 