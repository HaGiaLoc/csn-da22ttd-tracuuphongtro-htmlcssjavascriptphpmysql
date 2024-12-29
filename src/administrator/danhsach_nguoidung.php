<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

// Lấy danh sách người dùng từ database
$sql = "SELECT * FROM nguoiDung ORDER BY maNguoiDung DESC";
$result = $conn->query($sql);
?>

<div class="admin-table">
    <div class="table-actions">
        <div class="search-box">
            <a href="admin_dashboard.php?page=them_nguoidung" class="add-new-btn">
                <i class="fas fa-plus"></i>
                Thêm
            </a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mã người dùng</th>
                <th>Tên người dùng</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Ngày đăng ký</th>
                <th>Vai trò</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['maNguoiDung']}</td>";
                    echo "<td>{$row['tenNguoiDung']}</td>";
                    echo "<td>{$row['emailNguoiDung']}</td>";
                    echo "<td>{$row['sdtNguoiDung']}</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['ngayDangKy'])) . "</td>";
                    echo "<td><span class='role-badge " . $row['vaiTro'] . "'>" . 
                         ($row['vaiTro'] === 'admin' ? 'Quản trị viên' : 'Người dùng') . 
                         "</span></td>";
                    echo "<td class='action-buttons'>
                            <a href='admin_dashboard.php?page=sua_nguoidung&id={$row['maNguoiDung']}' class='edit-btn'>
                                <i class='fas fa-edit'></i> Sửa
                            </a>";
                    if ($row['vaiTro'] !== 'admin') {
                        echo "<a href='admin_dashboard.php?page=xoa_nguoidung&id={$row['maNguoiDung']}' 
                                class='delete-btn' 
                                onclick='return confirm(\"Bạn có chắc chắn muốn xóa người dùng này?\")'>
                                <i class='fas fa-trash'></i> Xóa
                            </a>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Không có người dùng nào</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div> 

<script>
function normalizeString(str) {
    return str.toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
}

document.getElementById('searchInput').addEventListener('input', function() {
    const searchValue = normalizeString(this.value);
    const rows = document.getElementById('tableBody').getElementsByTagName('tr');
    
    for (let row of rows) {
        const text = normalizeString(row.textContent);
        if (text.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});
</script> 