
<?php
function getAreaText($value) {
    $areas = [
        '0-10' => 'Dưới 10 m²',
        '10-20' => '10 m² - 20 m²',
        '20-30' => '20 m² - 30 m²',
        '30-40' => '30 m² - 40 m²',
        '40-50' => '40 m² - 50 m²',
        'above-50' => 'Trên 50 m²'
    ];
    return isset($areas[$value]) ? $areas[$value] : 'Diện tích';
}

// Lấy các giá trị từ URL (nếu có)
$selected_location = isset($_GET['location']) ? $_GET['location'] : '';
$min_price = isset($_GET['gia_toi_thieu']) ? $_GET['gia_toi_thieu'] : '';
$max_price = isset($_GET['gia_toi_da']) ? $_GET['gia_toi_da'] : '';
$selected_area = isset($_GET['dien_tich']) ? $_GET['dien_tich'] : '';
?>

<div class="header-container">
    <header>
        <!--Logo trang web-->
        <a href="index.php?page=trangchu">
            <img src="../images/logo.png" alt="Logo trang chủ" width="130px" height="130px">
        </a>
        <!--Thanh định hướng-->
        <div class="navigation-bar">
            <nav>
                <a href="index.php?page=trangchu" class="active">TRANG CHỦ</a>
                <a href="index.php?page=phongtro">PHÒNG TRỌ CHO THUÊ</a>
            </nav>
        </div>
        <!-- Nút đăng nhập -->
        <div class="login-container">
            <span id="user-info"></span>
            <button onclick="openModal()" id="login-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                &nbsp;&nbsp;Đăng nhập
            </button>
            <button id="logout-btn" class="logout" style="display: none;">
                Đăng xuất
            </button>
        </div>
        <!-- Form đăng nhập và đăng ký -->
        <div id="login-modal" class="modal" <?php echo isset($_GET['show_modal']) ? 'style="display:block;"' : ''; ?>>
            <div class="modal-content">
                
                <span class="close" onclick="window.location.href='index.php';">&times;</span>
                <div id="error-msg" style="display: none;"></div>
                <!-- Thông báo lỗi/thành công -->
                <?php if(isset($_SESSION['message'])): ?>
                    <div id="error-msg" style="display:block; color:<?php echo $_SESSION['message_type'] == 'success' ? '#28a745' : '#dc3545'; ?>">
                        <?php 
                            echo $_SESSION['message']; 
                            unset($_SESSION['message']);
                            unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <!-- Đăng Nhập -->
                <div class="form-section">
                    <h4>Đăng Nhập</h4>
                    <form id="login-form">
                        <label for="login-username">Tên đăng nhập</label>
                        <input type="text" id="login-username" name="username" required>
                        
                        <label for="login-password">Mật khẩu</label>
                        <input type="password" id="login-password" name="password">
                        
                        <div class="button-container">
                            <input type="submit" value="Đăng Nhập">
                            <a href="index.php?page=quenmatkhau" class="forgot-password">Quên mật khẩu?</a>
                        </div>
                    </form>
                </div>

                <!-- Đường kẻ phân chia -->
                <div class="divider"></div>

                <!-- Đăng Ký -->
                <div class="form-section">
                    <h4>Đăng Ký</h4>
                    <form action="dangky.php" method="POST">
                        <label for="register-fullname">Tên người dùng*</label>
                        <input type="text" 
                               id="register-fullname" 
                               name="fullname" 
                               required
                               oninvalid="this.setCustomValidity('Vui lòng điền vào trường này.')"
                               oninput="this.setCustomValidity('')">
                        
                        <label for="register-username">Tên đăng nhập*</label>
                        <input type="text" 
                               id="register-username" 
                               name="username" 
                               required
                               oninvalid="this.setCustomValidity('Vui lòng điền vào trường này.')"
                               oninput="this.setCustomValidity('')">
                        
                        <label for="register-password">Mật khẩu*</label>
                        <input type="password" 
                               id="register-password" 
                               name="password" 
                               required
                               oninvalid="this.setCustomValidity('Vui lòng điền vào trường này.')"
                               oninput="this.setCustomValidity('')">
                        
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email">
                        
                        <label for="register-phone">Số điện thoại</label>
                        <input type="tel" id="register-phone" name="phone" pattern="[0-9]{10}">
                        
                        <div class="button-container">
                            <input type="submit" value="Đăng Ký">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </header>
<!-- Thêm thanh tìm kiếm -->
    <div class="search-bar">
        <form action="index.php?page=phongtro" method="GET" class="form-tim-kiem">
            <input type="hidden" name="page" value="phongtro">
            
            <div class="dropdown">
                <div class="dropdown-btn" onclick="toggleDropdown(this.parentElement)">
                    <div class="selected-container">
                        <span class="selected-text"><?php echo $selected_location ? $selected_location : 'Vị trí'; ?></span>
                    </div>
                    <div class="icons-container">
                        <i class="fas fa-times clear-selection" onclick="clearSelection(event, 'location')" <?php echo !$selected_location ? 'style="display:none;"' : ''; ?>></i>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                </div>
                <div class="dropdown_content-location">
                    <div class="location-options">
                        <?php
                        $locations = ['Xã Long Đức', 'Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 
                                     'Phường 5', 'Phường 6', 'Phường 7', 'Phường 8', 'Phường 9'];
                        foreach ($locations as $location) {
                            $checked = ($selected_location === $location) ? 'checked' : '';
                            echo "<label id='location-option'>
                                    <input type='radio' name='location' value='$location' $checked>
                                    $location
                                </label>";
                        }
                        ?>
                    </div>
                    <button type="button" onclick="applyDropdown('location')">Áp dụng</button>
                </div>
            </div>

            <div class="price">
                <input type="number" min = "0" max = "5000000" step="10000" name="gia_toi_thieu" class="price-input" value="0"
                       placeholder="Giá tối thiểu (mặc định 0)" value="<?php echo $min_price; ?>">
                <span>-</span>
                <input type="number" min = "5000000" max = "10000000" step="10000" name="gia_toi_da" class="price-input" value="5000000"
                       placeholder="Giá tối đa" value="<?php echo $max_price; ?>">
            </div>

            <div class="dropdown">
                <div class="dropdown-btn" onclick="toggleDropdown(this.parentElement)">
                    <div class="selected-container">
                        <span class="selected-text"><?php echo $selected_area ? getAreaText($selected_area) : 'Diện tích'; ?></span>
                    </div>
                    <div class="icons-container">
                        <i class="fas fa-times clear-selection" onclick="clearSelection(event, 'area')" <?php echo !$selected_area ? 'style="display:none;"' : ''; ?>></i>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                </div>
                <div class="dropdown_content-area">
                    <div class="area-options">
                        <?php
                        $areas = [
                            '0-10' => 'Dưới 10 m²',
                            '10-20' => '10 m² - 20 m²',
                            '20-30' => '20 m² - 30 m²',
                            '30-40' => '30 m² - 40 m²',
                            '40-50' => '40 m² - 50 m²',
                            'above-50' => 'Trên 50 m²'
                        ];
                        foreach ($areas as $value => $text) {
                            $checked = ($selected_area === $value) ? 'checked' : '';
                            echo "<label id='area-option'>
                                    <input type='radio' name='dien_tich' value='$value' $checked>
                                    $text
                                </label>";
                        }
                        ?>
                    </div>
                    <button type="button" onclick="applyDropdown('area')">Áp dụng</button>
                </div>
            </div>

            <button type="submit" class="search-button">Tìm kiếm</button>
        </form>   
    </div>
    </div>
<script src="../javascript.js"></script>
<script src="../dangnhap.js"></script>
