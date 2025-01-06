<?php
// Import file kết nối CSDL
require_once 'db_connection.php';

// Hàm lấy thông tin phòng trọ theo ID
function getPhongTroById($conn, $id) {
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
        kt.googleMap,
        hp.hinhAnh,
        lp.tenLoaiPhong,
        kt.tenKhuTro,
        COALESCE(AVG(dg.diemSo), 0) as diemSo,
        COUNT(dg.maDanhGia) as soLuotDanhGia
    FROM phongtro pt
    JOIN khutro kt ON pt.maKhuTro = kt.maKhuTro
    JOIN loaiphong lp ON pt.maLoaiPhong = lp.maLoaiPhong
    JOIN hinhanhphong hp ON pt.maPhongTro = hp.maPhongTro
    LEFT JOIN danhgia dg ON pt.maPhongTro = dg.maPhongTro
    WHERE pt.maPhongTro = ?
    GROUP BY pt.maPhongTro";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    return null;
}

// Hàm tạo sao đánh giá
function createRatingStars($score) {
    $fullStars = floor($score);
    $hasHalfStar = ($score - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    
    $ratingHtml = '';
    
    for ($i = 0; $i < $fullStars; $i++) {
        $ratingHtml .= '<i class="fas fa-star"></i>';
    }
    
    if ($hasHalfStar) {
        $ratingHtml .= '<i class="fas fa-star-half-alt"></i>';
    }
    
    for ($i = 0; $i < $emptyStars; $i++) {
        $ratingHtml .= '<i class="far fa-star"></i>';
    }
    
    return $ratingHtml;
}

// Hàm lấy đánh giá theo ID phòng trọ
function getDanhGia($conn, $maPhongTro) {
    $sql = "SELECT dg.*, nd.tenNguoiDung 
            FROM danhgia dg 
            JOIN nguoidung nd ON dg.maNguoiDung = nd.maNguoiDung 
            WHERE dg.maPhongTro = ?
            ORDER BY dg.ngayNhanXet DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maPhongTro);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Hàm tạo mã đánh giá mới
function createNewMaDanhGia($conn) {
    $sql = "SELECT MAX(CAST(SUBSTRING(maDanhGia, 3) AS UNSIGNED)) as max_id FROM danhgia";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $nextId = str_pad(($row['max_id'] ?? 0) + 1, 5, '0', STR_PAD_LEFT);
    return 'DG' . $nextId;
}

// Xử lý khi nhấn nút gửi đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Vui lòng đăng nhập để đánh giá!');</script>";
    } else {
        $maDanhGia = createNewMaDanhGia($conn);
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $maPhongTro = $_POST['maPhongTro'];
        $diemSo = $_POST['rating'];
        $nhanXet = $_POST['review_text'];
        
        $sql = "INSERT INTO danhgia (maDanhGia, maNguoiDung, maPhongTro, diemSo, nhanXet, ngayNhanXet) 
                VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $maDanhGia, $maNguoiDung, $maPhongTro, $diemSo, $nhanXet);
        
        if ($stmt->execute()) {
            echo "<script>alert('Đánh giá của bạn đã được ghi nhận!');</script>";
            echo "<script>window.location.href = 'index.php?page=phongtro&id=" . $maPhongTro . "';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra khi gửi đánh giá!');</script>";
        }
    }
}

// Xử lý khi có ID phòng trọ
if (isset($_GET['id'])) {
    $phongTroId = $_GET['id'];
    $phongTro = getPhongTroById($conn, $phongTroId);
    $danhGiaList = getDanhGia($conn, $phongTroId);
    
    if ($phongTro) {
        ?>
            <!DOCTYPE html>
            <html lang="vi">
            <head>
                <meta charset="UTF-8">
                <title><?php echo $phongTro['tenLoaiPhong'] . ' - ' . $phongTro['tenKhuTro']; ?></title>
                <link rel="stylesheet" href="../styles.css">
                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            </head>
            <body>
                <?php include 'header.php'; ?>
                <div class="chitiet-phongtro">
                    <div class="thong-tin-header">
                        <div class="product-gallery">
                            <div class="slideshow-container">
                                <?php
                                $sql = "SELECT anhDaiDien, hinhAnh FROM hinhanhphong WHERE maPhongTro = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("s", $phongTroId);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                if ($row = $result->fetch_assoc()) {
                                    $images = array_filter(explode("\n", $row['hinhAnh']));
                                    
                                    foreach ($images as $index => $image) {
                                        echo '<div class="slide fade">';
                                        echo '<img src="' . htmlspecialchars($image) . '" alt="Ảnh phòng trọ">';
                                        echo '</div>';
                                    }
                                    if (count($images) > 1) {
                                        echo '<a class="prev" onclick="changeSlide(-1)">&#10094;</a>';
                                        echo '<a class="next" onclick="changeSlide(1)">&#10095;</a>';
                                        
                                        echo '<div class="dots-container">';
                                        foreach ($images as $index => $image) {
                                            echo '<span class="dot" onclick="currentSlide(' . ($index + 1) . ')"></span>';
                                        }
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        
                            <div class="thong-tin-header-right-left">
                                <div class="gia-ca">
                                    <h3>Giá cả</h3>
                                    <p><?php echo number_format($phongTro['giaPhong'], 0, ',', '.'); ?> VND/Tháng</p>
                                </div>
                                <div class="dien-tich">
                                    <h3>Diện tích phòng</h3>
                                    <p><?php echo $phongTro['dienTich']; ?> m²</p>
                                </div>
                                <div class="vi-tri">
                                    <h3>Địa chỉ</h3>
                                    <p><?php echo $phongTro['diaChi']; ?></p>
                                </div>
                            </div>
                            <div class="thong-tin-header-right-right">
                                <div class="ten-chu-tro">
                                    <h3>Tên chủ trọ</h3>
                                    <p><?php echo $phongTro['chuTro']; ?></p>
                                </div>
                                <div class="so-dien-thoai">
                                    <h3>Số điện thoại</h3>
                                    <p><?php echo $phongTro['sdtChuTro']; ?></p>
                                </div>
                                <div class="zalo">
                                    <h3>Zalo</h3>
                                    <p><?php echo $phongTro['sdtChuTro']; ?></p>
                                </div>
                            </div>
                    </div>
                    <div class="thong-tin-container">
                        <div class="thong-tin-chi-tiet">
                            <p style="font-size: 26px; font-weight: bold;"><?php echo $phongTro['tenKhuTro']; ?></p>
                            <br>
                            <p>Ngày đăng: <?php echo date('d/m/Y', strtotime($phongTro['ngayDang'])); ?></p>
                            <br>
                            <p>Tình trạng: <?php echo $phongTro['tinhTrang'] ? 'Còn trống' : 'Đã cho thuê'; ?></p>
                            <div class="danh-gia-header">
                                <h3>Đánh giá</h3>
                                <div class="rating">
                                    <?php
                                    $score = (float)$phongTro['diemSo'];
                                    echo createRatingStars($score);
                                    ?>
                                    <span class="score-text">(<?php echo number_format($score, 1) . ' / 5) (' . $phongTro['soLuotDanhGia']; ?>)</span>
                                </div>
                            </div>
                            <br>
                            <h3>Giới thiệu phòng trọ</h3>
                            <p><?php echo nl2br($phongTro['moTaPhongTro']); ?></p>

                            <div class="map-container">
                                <h3>Vị trí trên bản đồ</h3>
                                <?php if (!empty($phongTro['googleMap'])): ?>
                                    <iframe 
                                        width="100%" 
                                        height="600" 
                                        style="border:0; border-radius: 8px; margin: 20px 0; aspect-ratio: 1;" 
                                        loading="lazy" 
                                        allowfullscreen 
                                        src="https://maps.google.com/maps?q=<?php echo urlencode($phongTro['googleMap']); ?>&t=&z=17&ie=UTF8&iwloc=&output=embed">
                                    </iframe>
                                <?php else: ?>
                                    <p>Chưa có vị trí trên bản đồ</p>
                                <?php endif; ?>
                            </div>

                            <div class="dich-vu-container">
                                <h3>Dịch vụ</h3>
                                <?php
                                $sql = "SELECT dv.tenDichVu, dv.moTaDichVu, dv.donVi, ktdv.giaCa 
                                        FROM phongtro pt
                                        JOIN khutro kt ON pt.maKhuTro = kt.maKhuTro
                                        JOIN khuTro_dichVu ktdv ON kt.maKhuTro = ktdv.maKhuTro
                                        JOIN dichVu dv ON ktdv.maDichVu = dv.maDichVu
                                        WHERE pt.maPhongTro = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("s", $phongTroId);
                                $stmt->execute();
                                $dichvu_result = $stmt->get_result();

                                if ($dichvu_result->num_rows > 0) {
                                    echo '<div class="dich-vu-list">';
                                    while ($dichvu = $dichvu_result->fetch_assoc()) {
                                        echo '<div class="dich-vu-item">';
                                        echo '<div class="dich-vu-icon"><i class="fas fa-concierge-bell"></i></div>';
                                        echo '<div class="dich-vu-info">';
                                        echo '<h4>' . htmlspecialchars($dichvu['tenDichVu']) . '</h4>';
                                        echo '<p>' . htmlspecialchars($dichvu['moTaDichVu']) . '</p>';
                                        echo '<p class="dich-vu-gia">' . number_format($dichvu['giaCa'], 0, ',', '.') . ' VND/' . htmlspecialchars($dichvu['donVi']) . '</p>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                } else {
                                    echo '<p>Không có dịch vụ nào.</p>';
                                }
                                ?>
                            </div>
                        </div>
                       
                        <div class="danh-gia-chi-tiet">
                            <div class="danh-gia-container">
                                <?php if (isset($_SESSION['user'])): ?>
                                    <div class="review-form">
                                        <h4>Viết đánh giá của bạn</h4>
                                        <form method="POST" action="">
                                            <input type="hidden" name="maPhongTro" value="<?php echo $phongTroId; ?>">
                                            <div class="rating-input">
                                                <label>Đánh giá của bạn:</label>
                                                <select name="rating" required>
                                                    <option value="">Chọn số sao</option>
                                                    <option value="1">1 sao</option>
                                                    <option value="2">2 sao</option>
                                                    <option value="3">3 sao</option>
                                                    <option value="4">4 sao</option>
                                                    <option value="5">5 sao</option>
                                                </select>
                                            </div>
                                            <div class="review-text">
                                                <label>Nhận xét của bạn:</label>
                                                <textarea name="review_text" required></textarea>
                                            </div>
                                            <button type="submit" name="submit_review">Gửi đánh giá</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="login-prompt">
                                        <p>Vui lòng đăng nhập và tải lại trang để viết đánh giá</p>
                                    </div>
                                <?php endif; ?>
                                <!-- Danh sách đánh giá -->
                                <div class="review-list">
                                    <h4>Nhận xét</h4>
                                    <?php if (empty($danhGiaList)): ?>
                                        <p>Chưa có nhận xét nào.</p>
                                    <?php else: ?>
                                        <?php foreach ($danhGiaList as $danhGia): ?>
                                            <div class="review-item">
                                                <div class="review-user">
                                                    <div class="reviewer-name"><?php echo htmlspecialchars($danhGia['tenNguoiDung']); ?></div>
                                                    <div class="review-meta">
                                                        <div class="rating">
                                                            <?php echo createRatingStars($danhGia['diemSo']); ?>
                                                        </div>
                                                        <span class="review-date">
                                                            <?php echo date('d/m/Y', strtotime($danhGia['ngayNhanXet'])); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="review-content">
                                                    <?php echo nl2br(htmlspecialchars($danhGia['nhanXet'])); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'footer.php'; ?>
                <script>
                let slideIndex = 1;
                showSlides(slideIndex);

                function changeSlide(n) {
                    showSlides(slideIndex += n);
                }

                function currentSlide(n) {
                    showSlides(slideIndex = n);
                }

                function showSlides(n) {
                    let i;
                    let slides = document.getElementsByClassName("slide");
                    let dots = document.getElementsByClassName("dot");
                    
                    if (n > slides.length) {slideIndex = 1}    
                    if (n < 1) {slideIndex = slides.length}
                    
                    for (i = 0; i < slides.length; i++) {
                        slides[i].style.display = "none";  
                    }
                    for (i = 0; i < dots.length; i++) {
                        dots[i].className = dots[i].className.replace(" active-dot", "");
                    }
                    
                    slides[slideIndex-1].style.display = "block";  
                    dots[slideIndex-1].className += " active-dot";
                }

                let autoSlide = setInterval(() => {
                    changeSlide(1);
                }, 5000); 

                // Dừng auto slide khi hover vào slideshow
                document.querySelector('.slideshow-container').addEventListener('mouseover', () => {
                    clearInterval(autoSlide);
                });

                // Tiếp tục auto slide khi không hover
                document.querySelector('.slideshow-container').addEventListener('mouseout', () => {
                    autoSlide = setInterval(() => {
                        changeSlide(1);
                    }, 5000);
                });
                </script>
            </body>
            </html>
        <?php
    } else {
        echo "Không tìm thấy phòng trọ";
    }
} else {
    echo "Không tìm thấy mã phòng trọ";
}

// Đóng kết nối CSDL
closeConnection($conn);
?> 