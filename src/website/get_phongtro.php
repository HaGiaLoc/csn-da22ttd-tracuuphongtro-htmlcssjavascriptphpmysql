<?php
require_once 'db_connection.php';

function createPhongTroElement($phong) {
    $imagePath = $phong['anhDaiDien'];
    $score = isset($phong['diemTrungBinh']) ? (float)$phong['diemTrungBinh'] : 0;
    $fullStars = floor($score);
    $hasHalfStar = ($score - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    
    // Tạo HTML cho rating stars
    $ratingHtml = '';
    // Hiển thị sao đầy
    for ($i = 0; $i < $fullStars; $i++) {
        $ratingHtml .= '<i class="fas fa-star"></i>';
    }
    
    // Hiển thị nửa sao nếu có
    if ($hasHalfStar) {
        $ratingHtml .= '<i class="fas fa-star-half-alt"></i>';
    }
    
    // Hiển thị sao rỗng
    for ($i = 0; $i < $emptyStars; $i++) {
        $ratingHtml .= '<i class="far fa-star"></i>';
    }
    
    return '
    <article class="phongtro" data-id="' . $phong['maPhongTro'] . '">
        <a href="index.php?page=phongtro&id=' . $phong['maPhongTro'] . '" class="lien-ket-phong-tro">
            <div class="hinh-anh-review">
                <img src="' . $imagePath . '" alt="Hình ảnh phòng trọ">
            </div>
            <div class="details">
                <div class="thong-so">
                    <div class="price-area-row">
                        <span class="price-detail">' . number_format($phong['giaPhong'], 0, ',', '.') . ' VND/tháng</span>
                        <span class="area-detail">' . $phong['dienTich'] . ' m²</span>
                    </div>
                    <span class="place-detail">' . $phong['diaChi'] . '</span>
                </div>
                <div class="thong-tin">
                    <div class="time-rating">
                        <span class="time"><i class="fas fa-clock"></i> ' . date('d/m/Y', strtotime($phong['ngayDang'])) . '</span>
                        <div class="rating">
                            ' . $ratingHtml . '
                            <span class="score-text">(' . number_format($score, 1) . ') (' . $phong['soLuotDanhGia'] . ')</span>
                        </div>
                    </div>
                    <p class="limit-text">' . $phong['moTaPhongTro'] . '</p>
                </div>
            </div> 
        </a>
    </article>';
}

try {
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
    JOIN hinhanhphong hp ON pt.maPhongTro = hp.maPhongTro
    LEFT JOIN danhgia dg ON pt.maPhongTro = dg.maPhongTro
    WHERE pt.tinhTrang = 'empty'
    GROUP BY pt.maPhongTro
    ORDER BY pt.ngayDang DESC, diemTrungBinh DESC
    LIMIT 8";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Lỗi truy vấn: " . $conn->error);
    }
    
    $phongTroList = array();
    while ($row = $result->fetch_assoc()) {
        $phongTroList[] = $row;
    }
    
    if (empty($phongTroList)) {
        echo '<p>Không có phòng trọ nào.</p>';
    } else {
        echo '<div class="danhsachphongtro">';
        foreach ($phongTroList as $phong) {
            echo createPhongTroElement($phong);
        }
        echo '</div>';
    }
    
} catch (Exception $e) {
    error_log("Lỗi: " . $e->getMessage());
    echo 'Lỗi: ' . $e->getMessage();
}

closeConnection($conn);
?>