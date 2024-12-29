<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: adminDangNhap.php');
    exit();
}

$maPhongTro = $_GET['id'] ?? null;
if (!$maPhongTro) {
    header('Location: admin_dashboard.php?page=danhsach_phongtro');
    exit();
}

// Lấy danh sách dịch vụ có sẵn
$sql_dichvu = "SELECT maDichVu, tenDichVu FROM dichvu";
$result_dichvu = $conn->query($sql_dichvu);

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

    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // Cập nhật khu trọ
        $sql = "UPDATE khutro SET 
                tenKhuTro = ?,
                diaChi = ?,
                chuTro = ?,
                sdtChuTro = ?,
                googleMap = ?
                WHERE maKhuTro = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $tenKhuTro, $diaChi, $chuTro, $sdtChuTro, $_POST['googleMap'], $maKhuTro);
        $stmt->execute();

        // Xử lý cập nhật giá dịch vụ
        if (isset($_POST['selectedDichVu']) && is_array($_POST['selectedDichVu'])) {
            // Xóa tất cả dịch vụ cũ của khu trọ
            $sql_delete = "DELETE FROM khuTro_dichVu WHERE maKhuTro = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("s", $maKhuTro);
            $stmt_delete->execute();

            // Cập nhật giá cho các dịch vụ đã chọn
            foreach ($_POST['selectedDichVu'] as $key => $maDichVu) {
                $giaCa = $_POST['giaDichVu'][$key] ?? 0;
                $sql_update = "UPDATE khuTro_dichVu SET giaCa = ? WHERE maKhuTro = ? AND maDichVu = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("iss", $giaCa, $maKhuTro, $maDichVu);
                
                // Nếu không có bản ghi nào được cập nhật (không tồn tại), thì thêm mới
                if ($stmt_update->execute() && $stmt_update->affected_rows === 0) {
                    $sql_insert = "INSERT INTO khuTro_dichVu (maKhuTro, maDichVu, giaCa) VALUES (?, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("ssi", $maKhuTro, $maDichVu, $giaCa);
                    $stmt_insert->execute();
                }
            }
        }

        // Cập nhật loại phòng
        $sql = "UPDATE loaiphong SET 
                tenLoaiPhong = ?,
                giaPhong = ?,
                moTaPhongTro = ?
                WHERE maLoaiPhong = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdss", $tenLoaiPhong, $giaPhong, $moTaPhongTro, $maLoaiPhong);
        $stmt->execute();

        // Cập nhật phòng trọ
        $sql = "UPDATE phongtro SET 
                maKhuTro = ?,
                maLoaiPhong = ?,
                dienTich = ?,
                tinhTrang = ?
                WHERE maPhongTro = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $maKhuTro, $maLoaiPhong, $dienTich, $tinhTrang, $maPhongTro);
        $stmt->execute();

        // Xử lý hình ảnh mới nếu có
        if (!empty($_FILES['images']['name'][0])) {
            $imageLinks = [];
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['images']['name'][$key];
                $file_path = "../../images/" . basename($file_name);
                
                if (move_uploaded_file($tmp_name, $file_path)) {
                    $imageLinks[] = $file_path;
                }
            }
            
            if (!empty($imageLinks)) {
                // Kiểm tra xem đã có hình ảnh cho phòng trọ chưa
                $sql = "SELECT * FROM hinhanhphong WHERE maPhongTro = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $maPhongTro);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Lấy danh sách ảnh hiện tại
                    $row = $result->fetch_assoc();
                    $currentImages = explode("\n", $row['hinhAnh']);
                    
                    // Thêm ảnh mới vào danh sách
                    $allImages = array_merge($currentImages, $imageLinks);
                    
                    // Loại bỏ các dòng trống
                    $allImages = array_filter($allImages, function($image) {
                        return !empty(trim($image));
                    });
                    
                    // Cập nhật bảng hinhanhphong
                    $sql = "UPDATE hinhanhphong SET hinhAnh = ? WHERE maPhongTro = ?";
                    $stmt = $conn->prepare($sql);
                    $hinhAnh = implode("\n", $allImages);
                    $stmt->bind_param("ss", $hinhAnh, $maPhongTro);
                    $stmt->execute();
                    
                    // Cập nhật ảnh đại diện nếu chưa có
                    if (empty($row['anhDaiDien'])) {
                        $anhDaiDien = $imageLinks[0]; // Lấy ảnh đầu tiên làm ảnh đại diện
                        $sql = "UPDATE hinhanhphong SET anhDaiDien = ? WHERE maPhongTro = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ss", $anhDaiDien, $maPhongTro);
                        $stmt->execute();
                    }
                } else {
                    // Thêm mới nếu chưa có
                    $sql = "INSERT INTO hinhanhphong (maPhongTro, anhDaiDien, hinhAnh) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $anhDaiDien = $imageLinks[0]; // Lấy ảnh đầu tiên làm ảnh đại diện
                    $hinhAnh = implode("\n", $imageLinks);
                    $stmt->bind_param("sss", $maPhongTro, $anhDaiDien, $hinhAnh);
                    $stmt->execute();
                }
            }
        }

        $conn->commit();
        echo "<script>alert('Cập nhật phòng trọ thành công!'); window.location.href='admin_dashboard.php?page=danhsach_phongtro';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Có lỗi xảy ra: " . $e->getMessage() . "');</script>";
    }
}

// Lấy thông tin chi tiết phòng trọ
$sql = "SELECT pt.*, kt.*, lp.*, hp.anhDaiDien, hp.hinhAnh
        FROM phongtro pt
        JOIN khutro kt ON pt.maKhuTro = kt.maKhuTro
        JOIN loaiphong lp ON pt.maLoaiPhong = lp.maLoaiPhong
        LEFT JOIN hinhanhphong hp ON pt.maPhongTro = hp.maPhongTro
        WHERE pt.maPhongTro = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maPhongTro);
$stmt->execute();
$phongTro = $stmt->get_result()->fetch_assoc();

// Lấy danh sách ảnh hiện tại
$current_images = [];
if (!empty($phongTro['hinhAnh'])) {
    $current_images = explode("\n", $phongTro['hinhAnh']);
}
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

.price-input-group {
    display: flex;
    align-items: center;
    gap: 5px;
}

.price-input-group input[type="number"] {
    width: 150px;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.price-input-group span {
    min-width: 80px;
    color: #666;
}

.dichvu-item input[type="checkbox"] {
    margin-right: 5px;
}

.dichvu-item input[type="number"]:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}
</style>

<div class="product-form">
    <h2>Sửa thông tin phòng trọ</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="maKhuTro">Mã khu trọ:</label>
            <input type="text" name="maKhuTro" value="<?php echo $phongTro['maKhuTro']; ?>" required>
        </div>
        <div class="form-group">
            <label for="maLoaiPhong">Mã loại phòng:</label>
            <input type="text" name="maLoaiPhong" value="<?php echo $phongTro['maLoaiPhong']; ?>" required>
        </div>
        <div class="form-group">
            <label for="maPhongTro">Mã phòng trọ:</label>
            <input type="text" name="maPhongTro" value="<?php echo $phongTro['maPhongTro']; ?>" required>
        </div>
        <div class="form-group">
            <label for="tenKhuTro">Tên khu trọ:</label>
            <input type="text" name="tenKhuTro" value="<?php echo $phongTro['tenKhuTro']; ?>" required>
        </div>
        <div class="form-group">
            <label for="tenLoaiPhong">Tên loại phòng:</label>
            <input type="text" name="tenLoaiPhong" value="<?php echo $phongTro['tenLoaiPhong']; ?>" required>
        </div>
        <div class="form-group">
            <label for="diaChi">Địa chỉ:</label>
            <input type="text" name="diaChi" value="<?php echo $phongTro['diaChi']; ?>" required>
        </div>
        <div class="form-group">
            <label for="giaPhong">Giá phòng:</label>
            <input type="number" min="0" name="giaPhong" value="<?php echo $phongTro['giaPhong']; ?>" required min="0">
        </div>
        <div class="form-group">
            <label for="dienTich">Diện tích (m²):</label>
            <input type="number" name="dienTich" value="<?php echo $phongTro['dienTich']; ?>" required min="1" step="0.1">
        </div>
        <div class="form-group">
            <label for="tinhTrang">Tình trạng:</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="tinhTrang" value="1" <?php echo $phongTro['tinhTrang'] ? 'checked' : ''; ?>> Còn trống
                </label>
                <label class="radio-label">
                    <input type="radio" name="tinhTrang" value="0" <?php echo !$phongTro['tinhTrang'] ? 'checked' : ''; ?>> Đã cho thuê
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="chuTro">Tên chủ trọ:</label>
            <input type="text" name="chuTro" value="<?php echo $phongTro['chuTro']; ?>" required>
        </div>
        <div class="form-group">
            <label for="sdtChuTro">Số điện thoại chủ trọ:</label>
            <input type="text" name="sdtChuTro" value="<?php echo $phongTro['sdtChuTro']; ?>" required>
        </div>

        <div class="form-group">
            <label for="googleMap">Địa chỉ Google Map:</label>
            <input type="text" name="googleMap" value="<?php echo $phongTro['googleMap']; ?>" placeholder="Nhập địa chỉ Google Map">
            <small>Ví dụ: 9.934331, 106.344772</small>
        </div>

        <div class="form-group">
            <label for="moTaPhongTro">Mô tả phòng trọ:</label>
            <textarea name="moTaPhongTro" rows="4" required><?php echo $phongTro['moTaPhongTro']; ?></textarea>
        </div>

        <!-- Dropdown chọn dịch vụ có sẵn -->
        <div class="form-group">
            <label for="selectedDichVu">Chọn dịch vụ và giá:</label>
            <div id="dichvu-container" class="dich-vu-list-input">
                <?php
                // Lấy danh sách tất cả dịch vụ và giá của khu trọ hiện tại
                $sql_all_dichvu = "SELECT dv.*, ktdv.giaCa 
                                 FROM dichvu dv 
                                 LEFT JOIN khuTro_dichVu ktdv ON dv.maDichVu = ktdv.maDichVu 
                                    AND ktdv.maKhuTro = ?
                                 ORDER BY dv.tenDichVu ASC";
                $stmt_all_dichvu = $conn->prepare($sql_all_dichvu);
                $stmt_all_dichvu->bind_param("s", $phongTro['maKhuTro']);
                $stmt_all_dichvu->execute();
                $result_all_dichvu = $stmt_all_dichvu->get_result();

                while ($dichvu = $result_all_dichvu->fetch_assoc()): 
                    $isChecked = $dichvu['giaCa'] !== null ? 'checked' : '';
                    $giaCa = $dichvu['giaCa'] ?? '';
                ?>
                    <div class="dichvu-item">
                        <label>
                            <input type="checkbox" name="selectedDichVu[]" value="<?php echo $dichvu['maDichVu']; ?>" <?php echo $isChecked; ?>>
                            <?php echo $dichvu['tenDichVu']; ?>
                        </label>
                        <div class="price-input-group">
                            <input type="number" min="0" name="giaDichVu[]" 
                                   value="<?php echo $giaCa; ?>" 
                                   placeholder="Nhập giá dịch vụ"
                                   <?php echo !$isChecked ? 'disabled' : ''; ?>>
                            <span>VND/<?php echo $dichvu['donVi']; ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="anhDaiDien">Hình ảnh:</label>
            <div class="current-images">
                <?php
                // Lấy thông tin hình ảnh
                $sql = "SELECT hinhAnh FROM hinhanhphong WHERE maPhongTro = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $maPhongTro);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $images = explode("\n", $row['hinhAnh']);
                    foreach ($images as $image):
                ?>
                <div class="image-item">
                    <img src="<?php echo $image; ?>" alt="Ảnh phòng trọ">
                    <button type="button" class="delete-image" 
                            data-image="<?php echo $image; ?>"
                            data-maphongtro="<?php echo $maPhongTro; ?>">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php 
                    endforeach;
                }
                ?>
            </div>
            <input type="file" name="images[]" accept="image/*" multiple>
            <small>Có thể chọn nhiều ảnh. Ảnh đầu tiên sẽ là ảnh đại diện nếu chưa có.</small>
        </div>

        <button type="submit" class="edit-btn">
            <i class="fas fa-save"></i> Lưu thay đổi
        </button>
    </form>
</div>

<script>
// Xử lý xóa ảnh
document.querySelectorAll('.delete-image').forEach(button => {
    button.addEventListener('click', async function() {
        if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
            const image = this.dataset.image;
            const maPhongTro = this.dataset.maphongtro;
            
            try {
                const response = await fetch('xoa_anh.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        image: image,
                        maPhongTro: maPhongTro
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    this.closest('.image-item').remove();
                    alert('Xóa ảnh thành công!');
                } else {
                    alert('Có lỗi xảy ra khi xóa ảnh!');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            }
        }
    });
});

// Xử lý bật/tắt input giá dịch vụ
document.querySelectorAll('.dichvu-item input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const priceInput = this.closest('.dichvu-item').querySelector('input[type="number"]');
        if (this.checked) {
            priceInput.disabled = false;
            priceInput.required = true;
            if (!priceInput.value) {
                priceInput.value = '0';
            }
        } else {
            priceInput.disabled = true;
            priceInput.required = false;
            priceInput.value = '';
        }
    });
});

// Kiểm tra giá trước khi submit
document.querySelector('form').addEventListener('submit', function(e) {
    const checkedServices = document.querySelectorAll('.dichvu-item input[type="checkbox"]:checked');
    let valid = true;
    
    checkedServices.forEach(checkbox => {
        const priceInput = checkbox.closest('.dichvu-item').querySelector('input[type="number"]');
        if (!priceInput.value || priceInput.value < 0) {
            valid = false;
            priceInput.style.borderColor = 'red';
        } else {
            priceInput.style.borderColor = '';
        }
    });

    if (!valid) {
        e.preventDefault();
        alert('Vui lòng nhập giá hợp lệ cho tất cả dịch vụ đã chọn!');
    }
});
</script> 