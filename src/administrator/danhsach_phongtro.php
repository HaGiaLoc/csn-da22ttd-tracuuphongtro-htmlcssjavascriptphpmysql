<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

// Lấy danh sách phòng trọ từ database
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
LEFT JOIN hinhanhphong hp ON pt.maPhongTro = hp.maPhongTro
ORDER BY pt.ngayDang DESC";

$result = $conn->query($sql);
?>

<div class="admin-table">
    <div class="table-actions">
        <div class="search-box">
            <a href="admin_dashboard.php?page=them_phongtro" class="add-new-btn">
                <i class="fas fa-plus"></i>
                Thêm
            </a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mã phòng trọ</th>
                <th>Hình ảnh</th>
                <th>Địa chỉ</th>
                <th>Diện tích</th>
                <th>Giá phòng</th>
                <th>Tình trạng</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Kiểm tra nếu không có ảnh thì dùng ảnh mặc định
                    $imagePath = $row['anhDaiDien'];
                    if (empty($imagePath) || !file_exists($imagePath)) {
                        $imagePath = "../../images/default.jpg";
                    }

                    echo "<tr>";
                    echo "<td>{$row['maPhongTro']}</td>";
                    echo "<td><img src='{$imagePath}' alt='Hình ảnh phòng trọ' class='product-image'></td>";
                    echo "<td>{$row['diaChi']}</td>";
                    echo "<td>{$row['dienTich']} m²</td>";
                    echo "<td>" . number_format($row['giaPhong'], 0, ',', '.') . " VND</td>";
                    echo "<td>" . ($row['tinhTrang'] ? 'Còn trống' : 'Đã cho thuê') . "</td>";
                    echo "<td class='action-buttons'>
                            <a href='admin_dashboard.php?page=sua_phongtro&id={$row['maPhongTro']}' class='edit-btn'>
                                <i class='fas fa-edit'></i> Sửa
                            </a>
                            <a href='admin_dashboard.php?page=xoa_phongtro&id={$row['maPhongTro']}' class='delete-btn' onclick='return confirm(\"Bạn có chắc chắn muốn xóa phòng trọ này?\")'>
                                <i class='fas fa-trash'></i> Xóa
                            </a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Không có phòng trọ nào</td></tr>";
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