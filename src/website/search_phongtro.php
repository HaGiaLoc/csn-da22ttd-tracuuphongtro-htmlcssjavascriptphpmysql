<?php
require_once 'db_connection.php'; // Yêu cầu tệp kết nối cơ sở dữ liệu

// Hàm tạo phần tử phòng trọ
function createPhongTroElement($phong) {
    $imagePath = !empty($phong['anhDaiDien']) ? $phong['anhDaiDien'] : 'default_image.jpg'; // Xác định đường dẫn hình ảnh
    
    $score = isset($phong['diemTrungBinh']) ? (float)$phong['diemTrungBinh'] : 0; // Tính điểm trung bình
    $fullStars = floor($score); // Số sao đầy đủ
    $hasHalfStar = $score - $fullStars >= 0.5; // Kiểm tra xem có nửa sao không
    
    $ratingHtml = ''; // Khởi tạo HTML cho đánh giá
    for ($i = 1; $i <= $fullStars; $i++) {
        $ratingHtml .= '<i class="fas fa-star"></i>'; // Thêm sao đầy đủ
    }
    if ($hasHalfStar) {
        $ratingHtml .= '<i class="fas fa-star-half-alt"></i>'; // Thêm nửa sao
        $fullStars++; // Tăng số sao đầy đủ nếu có nửa sao
    }
    for ($i = $fullStars + ($hasHalfStar ? 0 : 1); $i <= 5; $i++) {
        $ratingHtml .= '<i class="far fa-star"></i>'; // Thêm sao rỗng
    }
    
    return '
    <article class="phongtro" data-id="' . htmlspecialchars($phong['maPhongTro']) . '">
        <a href="index.php?page=phongtro&id=' . htmlspecialchars($phong['maPhongTro']) . '" class="lien-ket-phong-tro">
            <div class="hinh-anh-review">
                <img src="' . htmlspecialchars($imagePath) . '" alt="Hình ảnh phòng trọ">
            </div>
            <div class="details">
                <div class="thong-so">
                    <div class="price-area-row">
                        <span class="price-detail">' . number_format($phong['giaPhong'], 0, ',', '.') . ' VND/tháng</span>
                        <span class="area-detail">' . htmlspecialchars($phong['dienTich']) . ' m²</span>
                    </div>
                    <span class="place-detail">' . htmlspecialchars($phong['diaChi']) . '</span>
                </div>
                <div class="thong-tin">
                    <div class="time-rating">
                        <span class="time"><i class="fas fa-clock"></i> ' . htmlspecialchars(date('d/m/Y', strtotime($phong['ngayDang']))) . '</span>
                        <div class="rating">
                            ' . $ratingHtml . '
                            <span class="score-text">(' . number_format($score, 1) . ')</span>
                        </div>
                    </div>
                    <p class="limit-text">' . htmlspecialchars($phong['moTaPhongTro']) . '</p>
                </div>
            </div> 
        </a>
    </article>';
}

// Hàm tìm kiếm phòng trọ
function searchPhongTro($conn, $location = null, $gia_toi_thieu = null, $gia_toi_da = null, $dien_tich = null) {
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
        hp.anhDaiDien,
        COALESCE(AVG(dg.diemSo), 0) as diemTrungBinh,
        COUNT(dg.maDanhGia) as soLuotDanhGia
    FROM phongtro pt
    JOIN khutro kt ON pt.maKhuTro = kt.maKhuTro
    JOIN loaiphong lp ON pt.maLoaiPhong = lp.maLoaiPhong
    LEFT JOIN hinhanhphong hp ON pt.maPhongTro = hp.maPhongTro
    LEFT JOIN danhgia dg ON pt.maPhongTro = dg.maPhongTro
    WHERE pt.tinhTrang = 'empty'"; // Truy vấn cơ bản
    
    $params = []; // Mảng tham số
    $types = ""; // Chuỗi kiểu dữ liệu

    if ($location) {
        $sql .= " AND LOWER(kt.diaChi) LIKE LOWER(?)"; // Thêm điều kiện tìm kiếm theo địa chỉ
        $params[] = "%$location%";
        $types .= "s";
    }

    if ($gia_toi_thieu !== null) {
        $sql .= " AND lp.giaPhong >= ?"; // Thêm điều kiện tìm kiếm theo giá thấp nhất
        $params[] = $gia_toi_thieu;
        $types .= "i";
    }
    if ($gia_toi_da !== null) {
        $sql .= " AND lp.giaPhong <= ?"; // Thêm điều kiện tìm kiếm theo giá cao nhất
        $params[] = $gia_toi_da;
        $types .= "i";
    }

    if ($dien_tich) {
        if ($dien_tich === 'above-50') {
            $sql .= " AND pt.dienTich > 50"; // Thêm điều kiện tìm kiếm theo diện tích lớn hơn 50
        } else {
            list($min, $max) = explode('-', $dien_tich); // Phân tách giá trị diện tích
            $sql .= " AND pt.dienTich BETWEEN ? AND ?"; // Thêm điều kiện tìm kiếm theo khoảng diện tích
            $params[] = (float)$min;
            $params[] = (float)$max;
            $types .= "dd";
        }
    }

    $sql .= " GROUP BY pt.maPhongTro
              ORDER BY pt.ngayDang DESC"; // Nhóm và sắp xếp kết quả

    try {
        $stmt = $conn->prepare($sql); // Chuẩn bị truy vấn
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params); // Gán tham số
        }
        $stmt->execute(); // Thực thi truy vấn
        $result = $stmt->get_result(); // Lấy kết quả
        return $result->fetch_all(MYSQLI_ASSOC); // Trả về kết quả dưới dạng mảng
    } catch (Exception $e) {
        error_log("Lỗi SQL: " . $e->getMessage()); // Ghi lại lỗi
        return []; // Trả về mảng rỗng nếu có lỗi
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $location = $_GET['location'] ?? null; // Lấy địa chỉ từ GET
    $gia_toi_thieu = isset($_GET['gia_toi_thieu']) && $_GET['gia_toi_thieu'] !== '' 
        ? max(0, (int)$_GET['gia_toi_thieu'])
        : 0; // Lấy giá thấp nhất từ GET
    
    $gia_toi_da = isset($_GET['gia_toi_da']) && $_GET['gia_toi_da'] !== '' 
        ? (int)$_GET['gia_toi_da']
        : null; // Lấy giá cao nhất từ GET
    
    $dien_tich = $_GET['dien_tich'] ?? null; // Lấy diện tích từ GET

    try {
        $results = searchPhongTro($conn, $location, $gia_toi_thieu, $gia_toi_da, $dien_tich); // Tìm kiếm phòng trọ

        if (empty($results)) {
            echo '<p>Không tìm thấy kết quả phù hợp.</p>'; // Hiển thị thông báo nếu không có kết quả
        } else {
            echo '<div class="danhsachphongtro">'; // Bắt đầu danh sách phòng trọ
            foreach ($results as $phong) {
                echo createPhongTroElement($phong); // Tạo phần tử phòng trọ
            }
            echo '</div>'; // Kết thúc danh sách phòng trọ
        }
    } catch (Exception $e) {
        error_log("Lỗi: " . $e->getMessage()); // Ghi lại lỗi
        echo '<p>Đã xảy ra lỗi trong quá trình tìm kiếm. Vui lòng thử lại sau.</p>'; // Hiển thị thông báo lỗi
    }
}
?>
