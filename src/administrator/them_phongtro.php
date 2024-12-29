<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

// Tạo mã khu trọ mới
$sql_khuTro = "SELECT MAX(CAST(SUBSTRING(maKhuTro, 3) AS UNSIGNED)) as max_id FROM khutro";
$result_khuTro = $conn->query($sql_khuTro);
$row_khuTro = $result_khuTro->fetch_assoc();
$nextKhuTroId = str_pad(($row_khuTro['max_id'] ?? 0) + 1, 5, '0', STR_PAD_LEFT);
$maKhuTro = 'KT' . $nextKhuTroId;

// Tạo mã loại phòng mới
$sql_loaiPhong = "SELECT MAX(CAST(SUBSTRING(maLoaiPhong, 3) AS UNSIGNED)) as max_id FROM loaiphong";
$result_loaiPhong = $conn->query($sql_loaiPhong);
$row_loaiPhong = $result_loaiPhong->fetch_assoc();
$nextLoaiPhongId = str_pad(($row_loaiPhong['max_id'] ?? 0) + 1, 5, '0', STR_PAD_LEFT);
$maLoaiPhong = 'LP' . $nextLoaiPhongId;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Thông tin khu trọ
    $maKhuTro = $_POST['maKhuTro'];
    $tenKhuTro = $_POST['tenKhuTro'];
    $diaChi = $_POST['diaChi'];
    $chuTro = $_POST['chuTro'];
    $sdtChuTro = $_POST['sdtChuTro'];

    // Thông tin loại phòng
    $maLoaiPhong = $_POST['maLoaiPhong'];
    $tenLoaiPhong = $_POST['tenLoaiPhong'];
    $giaPhong = $_POST['giaPhong'];
    $moTaPhongTro = $_POST['moTaPhongTro'];

    // Thông tin phòng trọ
    $dienTich = $_POST['dienTich'];
    $tinhTrang = $_POST['tinhTrang'];
    
    // Tạo mã phòng trọ mới
    $sql = "SELECT MAX(CAST(SUBSTRING(maPhongTro, 3) AS UNSIGNED)) as max_id FROM phongtro";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $nextId = str_pad(($row['max_id'] ?? 0) + 1, 5, '0', STR_PAD_LEFT);
    $maPhongTro = 'PT' . $nextId;

    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // Thêm/Cập nhật khu trọ
        $sql = "INSERT INTO khutro (maKhuTro, tenKhuTro, diaChi, chuTro, sdtChuTro, googleMap) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $maKhuTro, $tenKhuTro, $diaChi, $chuTro, $sdtChuTro, $_POST['googleMap']);
        $stmt->execute();

        // Xử lý thêm dịch vụ và giá
        if (isset($_POST['selectedDichVu']) && is_array($_POST['selectedDichVu'])) {
            foreach ($_POST['selectedDichVu'] as $key => $maDichVu) {
                $giaCa = $_POST['giaDichVu'][$key] ?? 0;
                $sql = "INSERT INTO khuTro_dichVu (maKhuTro, maDichVu, giaCa) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $maKhuTro, $maDichVu, $giaCa);
                $stmt->execute();
            }
        }

        // Thêm/Cập nhật loại phòng
        $sql = "INSERT INTO loaiphong (maLoaiPhong, tenLoaiPhong, giaPhong, moTaPhongTro) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                tenLoaiPhong = VALUES(tenLoaiPhong),
                giaPhong = VALUES(giaPhong),
                moTaPhongTro = VALUES(moTaPhongTro)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssds", $maLoaiPhong, $tenLoaiPhong, $giaPhong, $moTaPhongTro);
        $stmt->execute();

        // Xử lý nhiều hình ảnh
        $imageLinks = [];
        $anhDaiDien = "../../images/default_images.jpg"; // Đặt ảnh mặc định
        
        // Kiểm tra nếu có file được upload
        if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['images']['tmp_name'][$key];
                    $file_name = basename($name);
                    $file_path = "../../images/" . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $imageLinks[] = $file_path;
                    }
                }
            }
            
            // Nếu có ít nhất một ảnh được upload thành công
            if (!empty($imageLinks)) {
                $anhDaiDien = $imageLinks[0]; // Ảnh đầu tiên làm ảnh đại diện
            }
        }
        
        // Thêm phòng trọ mới
        $sql = "INSERT INTO phongtro (maPhongTro, maKhuTro, maLoaiPhong, dienTich, tinhTrang, ngayDang) 
                VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $maPhongTro, $maKhuTro, $maLoaiPhong, $dienTich, $tinhTrang);

        if ($stmt->execute()) {
            // Thêm hình ảnh vào bảng hinhanhphong
            $sql = "INSERT INTO hinhanhphong (maPhongTro, anhDaiDien, hinhAnh) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $hinhAnh = !empty($imageLinks) ? implode("\n", $imageLinks) : $anhDaiDien;
            $stmt->bind_param("sss", $maPhongTro, $anhDaiDien, $hinhAnh);
            $stmt->execute();

            echo "<script>alert('Thêm phòng trọ thành công!'); window.location.href='admin_dashboard.php';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra khi thêm phòng trọ!'); window.location.href='admin_dashboard.php?page=them_phongtro';</script>";
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Có lỗi xảy ra: " . $e->getMessage() . "');</script>";
    }
}

// Lấy danh sách khu trọ và loại phòng
$khuTroList = $conn->query("SELECT * FROM khutro");
$loaiPhongList = $conn->query("SELECT * FROM loaiphong");
?>

<style>
.dich-vu-list-input {
    margin-top: 10px;
}

.dichvu-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    gap: 10px;
}

.dichvu-item label {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 5px;
}

.dichvu-item input[type="number"] {
    width: 150px;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.dichvu-item input[type="checkbox"] {
    margin-right: 5px;
}
</style>

<div class="product-form">
    <h2>Thêm phòng trọ mới</h2>
    <form method="POST">
        <div class="form-group">
            <label for="maKhuTro">Mã khu trọ:</label>
            <input type="text" name="maKhuTro" value="<?php echo $maKhuTro; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="maLoaiPhong">Mã loại phòng:</label>
            <input type="text" name="maLoaiPhong" value="<?php echo $maLoaiPhong; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="tenKhuTro">Tên khu trọ:</label>
            <input type="text" name="tenKhuTro" required>
        </div>

        <div class="form-group">
            <label for="tenLoaiPhong">Tên loại phòng:</label>
            <input type="text" name="tenLoaiPhong" required>
        </div>
        
        <div class="form-group">
            <label for="diaChi">Địa chỉ:</label>
            <input type="text" name="diaChi" required>
        </div>
        
        <div class="form-group">
            <label for="giaPhong">Giá phòng:</label>
            <input type="number" name="giaPhong" required min="0" value="0">
        </div>

        <div class="form-group">
            <label for="dienTich">Diện tích (m²):</label>
            <input type="number" name="dienTich" required min="1" step="0.1">
        </div>

        <div class="form-group">
            <label for="chuTro">Tên chủ trọ:</label>
            <input type="text" name="chuTro" required>
        </div>
        
        <div class="form-group">
            <label for="sdtChuTro">Số điện thoại chủ trọ:</label>
            <input type="text" name="sdtChuTro" required>
        </div>
        
        <div class="form-group">
            <label for="googleMap">Địa chỉ Google Map:</label>
            <input type="text" name="googleMap" placeholder="Nhập địa chỉ Google Map">
            <small>Ví dụ: 9.934331, 106.344772</small>
        </div>
        
        <div class="form-group">
            <label for="tinhTrang">Tình trạng:</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="tinhTrang" value="1" checked> Còn trống
                </label>
                <label class="radio-label">
                    <input type="radio" name="tinhTrang" value="0"> Đã cho thuê
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="moTaPhongTro">Giới thiệu:</label>
            <textarea name="moTaPhongTro" rows="4" required></textarea>
        </div>

        <!-- Dropdown chọn dịch vụ có sẵn -->
        <div class="form-group">
            <label for="selectedDichVu">Chọn dịch vụ và giá:</label>
            <div id="dichvu-container" class="dich-vu-list-input">
                <?php
                // Lấy danh sách dịch vụ có sẵn
                $sql_dichvu = "SELECT maDichVu, tenDichVu, donVi FROM dichvu";
                $result_dichvu = $conn->query($sql_dichvu);
                while ($dichvu = $result_dichvu->fetch_assoc()): ?>
                    <div class="dichvu-item">
                        <label>
                            <input type="checkbox" name="selectedDichVu[]" value="<?php echo $dichvu['maDichVu']; ?>" onchange="togglePriceInput(this)">
                            <?php echo $dichvu['tenDichVu']; ?> (<?php echo $dichvu['donVi']; ?>)
                        </label>
                        <input type="number" min="0" name="giaDichVu[]" value="0" placeholder="Nhập giá dịch vụ" disabled>
                        <span>VND</span>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="anhDaiDien">Hình ảnh:</label>
            <input type="file" name="images[]" accept="image/*" multiple>
            <small>Có thể chọn nhiều ảnh. Ảnh đầu tiên sẽ là ảnh đại diện. Nếu không chọn ảnh, hệ thống sẽ sử dụng ảnh mặc định.</small>
        </div>

        <button type="submit" class="add-new-btn" onclick="return validateForm(event)">
            <i class="fas fa-plus"></i> Thêm phòng trọ
        </button>
    </form>
</div>

<script>
function validateForm(event) {
    event.preventDefault();
    
    // Lấy các trường input cần kiểm tra
    const tenKhuTro = document.querySelector('input[name="tenKhuTro"]').value.trim();
    const tenLoaiPhong = document.querySelector('input[name="tenLoaiPhong"]').value.trim();
    const diaChi = document.querySelector('input[name="diaChi"]').value.trim();
    const giaPhong = document.querySelector('input[name="giaPhong"]').value;
    const dienTich = document.querySelector('input[name="dienTich"]').value;
    const chuTro = document.querySelector('input[name="chuTro"]').value.trim();
    const sdtChuTro = document.querySelector('input[name="sdtChuTro"]').value.trim();
    const moTaPhongTro = document.querySelector('textarea[name="moTaPhongTro"]').value.trim();
    
    // Kiểm tra các trường bắt buộc
    if (!tenKhuTro) {
        alert('Vui lòng nhập tên khu trọ!');
        return false;
    }
    if (!tenLoaiPhong) {
        alert('Vui lòng nhập tên loại phòng!');
        return false;
    }
    if (!diaChi) {
        alert('Vui lòng nhập địa chỉ!');
        return false;
    }
    if (!giaPhong || giaPhong <= 0) {
        alert('Vui lòng nhập giá phòng hợp lệ!');
        return false;
    }
    if (!dienTich || dienTich <= 0) {
        alert('Vui lòng nhập diện tích hợp lệ!');
        return false;
    }
    if (!chuTro) {
        alert('Vui lòng nhập tên chủ trọ!');
        return false;
    }
    if (!sdtChuTro) {
        alert('Vui lòng nhập số điện thoại chủ trọ!');
        return false;
    }

    if (!moTaPhongTro) {
        alert('Vui lòng nhập mô tả phòng trọ!');
        return false;
    }
    
    // Kiểm tra dịch vụ đã chọn
    const selectedServices = document.querySelectorAll('input[name="selectedDichVu[]"]:checked');
    const servicePrices = document.querySelectorAll('input[name="giaDichVu[]"]');
    
    for (let i = 0; i < selectedServices.length; i++) {
        const priceInput = servicePrices[i];
        if (selectedServices[i].checked && (!priceInput.value || priceInput.value <= 0)) {
            alert('Vui lòng nhập giá cho tất cả các dịch vụ đã chọn!');
            return false;
        }
    }
    
    // Nếu tất cả đều hợp lệ, submit form
    document.querySelector('form').submit();
    return true;
}

function togglePriceInput(checkbox) {
    const priceInput = checkbox.closest('.dichvu-item').querySelector('input[type="number"]');
    priceInput.disabled = !checkbox.checked;
    if (!checkbox.checked) {
        priceInput.value = ''; // Xóa giá khi bỏ chọn dịch vụ
    }
}

// Thêm vào đầu file script hiện tại
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo trạng thái ban đầu cho tất cả các input giá
    document.querySelectorAll('.dichvu-item input[type="checkbox"]').forEach(checkbox => {
        togglePriceInput(checkbox);
    });
});
</script> 
