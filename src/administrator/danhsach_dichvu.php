<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

// Xử lý xóa dịch vụ
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $maDichVu = $_GET['id'];
    
    // Xóa dịch vụ
    $sql = "DELETE FROM dichvu WHERE maDichVu = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maDichVu);
    
    if ($stmt->execute()) {
        echo "<script>alert('Xóa dịch vụ thành công!'); window.location.href='admin_dashboard.php?page=danhsach_dichvu';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi xóa dịch vụ!'); window.location.href='admin_dashboard.php?page=danhsach_dichvu';</script>";
    }
}

// Lấy danh sách dịch vụ từ database
$sql = "SELECT * FROM dichvu ORDER BY maDichVu DESC";
$result = $conn->query($sql);
?>

<div class="admin-table">
    <div class="table-actions">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Tìm kiếm dịch vụ...">
            <a href="admin_dashboard.php?page=them_dichvu" class="add-new-btn">
                <i class="fas fa-plus"></i>
                Thêm
            </a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mã dịch vụ</th>
                <th>Tên dịch vụ</th>
                <th>Mô tả</th>
                <th>Đơn vị</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['maDichVu']}</td>";
                    echo "<td>{$row['tenDichVu']}</td>";
                    echo "<td>{$row['moTaDichVu']}</td>";
                    echo "<td>{$row['donVi']}</td>";
                    echo "<td class='action-buttons'>
                            <a href='admin_dashboard.php?page=danhsach_dichvu&action=delete&id={$row['maDichVu']}' 
                               class='delete-btn' 
                               onclick='return confirm(\"Bạn có chắc chắn muốn xóa dịch vụ này?\")'>
                                <i class='fas fa-trash'></i> Xóa
                            </a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Không có dịch vụ nào</td></tr>";
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