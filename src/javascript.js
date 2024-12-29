// Chú thích: Đoạn code này sẽ thực hiện khi trang web được tải hoàn toàn
document.addEventListener('DOMContentLoaded', function() {
    updateSliderLimits();
    initializeNavigation();
    initializeDropdowns();
    loadPhongTro();
    const priceInputs = document.querySelectorAll('.price-input');
    priceInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            const unit = this.nextElementSibling;
            unit.style.display = this.value ? 'block' : 'none';
        });
    });

    const locationText = document.querySelector('.location .selected-text');
    const locationClear = document.querySelector('.location .clear-selection');
    if (locationText.textContent !== 'Vị trí') {
        locationClear.style.display = 'inline-block';
    }

    const areaText = document.querySelector('.area .selected-text');
    const areaClear = document.querySelector('.area .clear-selection');
    if (areaText.textContent !== 'Diện tích') {
        areaClear.style.display = 'inline-block';
    }
});

// Chú thích: Hàm này sẽ khởi tạo thanh điều hướng
function initializeNavigation() {
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('href').split('=')[1];
            
            navLinks.forEach(link => link.classList.remove('active'));
            this.classList.add('active');
            
            changePage(page);
        });
    });
}

// Chú thích: Hàm này sẽ khởi tạo các dropdown
function initializeDropdowns() {
    const dropdownItems = document.querySelectorAll('.dropdown_content-location div, .dropdown_content-price div, .dropdown_content-area div');
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });

    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            const content = dropdown.querySelector('.dropdown_content-location, .dropdown_content-price, .dropdown_content-area');
            if (content.style.display === "block" && !dropdown.contains(event.target)) {
                content.style.display = "none";
            }
        });
    });
}

// Chú thích: Hàm này sẽ cập nhật giới hạn của thanh trượt
function updateSliderLimits() {
    const minPriceInput = document.getElementById('gia_toi_thieu');
    const maxPriceInput = document.getElementById('gia_toi_da');
    const slider = document.getElementById('slider_gia');

    if (!minPriceInput || !maxPriceInput || !slider) return;

    const minPrice = parseInt(minPriceInput.value);
    const maxPrice = parseInt(maxPriceInput.value);

    slider.min = minPrice;
    slider.max = maxPrice;

    slider.value = Math.min(Math.max(slider.value, minPrice), maxPrice);

    updatePriceInputs();
}

// Chú thích: Hàm này sẽ cập nhật giá trị của thanh trượt
function updatePriceInputs() {
    const slider = document.getElementById('slider_gia');
    const maxPriceInput = document.getElementById('gia_toi_da');

    if (!slider || !maxPriceInput) return;

    maxPriceInput.value = slider.value;

    const percentage = (slider.value - slider.min) / (slider.max - slider.min) * 100;
    slider.style.background = `linear-gradient(to right, #33CCFF ${percentage}%, #333 ${percentage}%)`;
}

// Chú thích: Hàm này sẽ mở hoặc đóng dropdown
function toggleDropdown(dropdown) {
    const content = dropdown.querySelector('[class^="dropdown_content"]');
    
    document.querySelectorAll('[class^="dropdown_content"]').forEach(item => {
        if (item !== content && item.style.display === 'block') {
            item.style.display = 'none';
        }
    });
    
    if (content.style.display === 'block') {
        content.style.display = 'none';
    } else {
        content.style.display = 'block';
    }
}

// Chú thích: Hàm này sẽ áp dụng dropdown
function applyDropdown(type) {
    const dropdown = document.querySelector(`.dropdown_content-${type}`).closest('.dropdown');
    const selectedText = dropdown.querySelector('.selected-text');
    const clearButton = dropdown.querySelector('.clear-selection');
    
    const selectedRadio = dropdown.querySelector('input[type="radio"]:checked');
    if (selectedRadio) {
        const label = selectedRadio.parentElement;
        selectedText.textContent = label.textContent.trim();
        clearButton.style.display = 'inline-block';  
    }
    
    dropdown.querySelector(`.dropdown_content-${type}`).style.display = 'none';
}

// Chú thích: Hàm này sẽ xóa lựa chọn
function clearSelection(event, type) {
    event.stopPropagation();  
    
    const dropdown = document.querySelector(`.dropdown_content-${type}`).closest('.dropdown');
    const selectedText = dropdown.querySelector('.selected-text');
    const clearButton = dropdown.querySelector('.clear-selection');
    const radioButtons = dropdown.querySelectorAll('input[type="radio"]');
    
    selectedText.textContent = type === 'location' ? 'Vị trí' : 'Diện tích';
    
    radioButtons.forEach(radio => {
        radio.checked = false;
    });
    
    clearButton.style.display = 'none';
}

// Chú thích: Hàm này sẽ chọn quận/huyện
function selectDistrict(dropdown) {
    const value = dropdown.value;
    const hamlets = document.querySelectorAll('.dropdown_content-hamlet');
    const labels = document.querySelectorAll('label[for="dropdown_content-hamlet"]');

    hamlets.forEach(hamlet => hamlet.style.display = 'none');
    labels.forEach(label => label.style.display = 'none');

    const districtMap = {
        'tp-travinh': 'TraVinh',
        'tx-duyenhai': 'tx_DuyenHai',
        'canglong': 'CangLong',
        'cauke': 'CauKe',
        'caungang': 'CauNgang',
        'h-duyenhai': 'h_DuyenHai',
        'chauthanh': 'ChauThanh',
        'tracu': 'TraCu',
        'tieucan': 'TieuCan'
    };

    if (districtMap[value]) {
        document.getElementById(districtMap[value]).style.display = 'block';
        document.getElementById(`label-${districtMap[value]}`).style.display = 'block';
    } else {
        console.log('Không có xã/phường tương ứng!');
    }
}

// Chú thích: Hàm này sẽ thay đổi trang
function changePage(page) {
    window.location.href = `index.php?page=${page}`;
}

// Chú thích: Hàm này sẽ tạo phần tử phòng trọ
function createPhongTroElement(phong) {
    function createRatingStars(rating) {
        let starsHtml = '';
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

        for (let i = 0; i < fullStars; i++) {
            starsHtml += '<i class="fas fa-star"></i>';
        }

        if (hasHalfStar) {
            starsHtml += '<i class="fas fa-star-half-alt"></i>';
        }

        for (let i = 0; i < emptyStars; i++) {
            starsHtml += '<i class="far fa-star"></i>';
        }

        return starsHtml;
    }

    return `
    <div class="danhsachphongtro">
        <article class="phongtro">
            <a href="#" class="lien-ket-phong-tro">
                <div class="hinh-anh-review">
                    <img src="data:image/jpeg;base64,${phong.anhDaiDien}" alt="Hình ảnh phòng trọ">
                </div>
                <div class="details">
                    <div class="thong-so">
                        <span class="price-detail">${formatCurrency(phong.giaThue)} VNĐ/tháng</span>
                        <span class="area-detail">${phong.dienTich} m²</span>
                        <span class="place-detail">${phong.diaChi}</span>
                    </div>
                    <div class="thong-tin">
                        <div class="time-rating">
                            <div class="time">
                                <i class="fas fa-clock"></i>
                                <span>${formatDate(phong.ngayDang)}</span>
                            </div>
                            <div class="rating">
                                ${createRatingStars(parseFloat(phong.danhGia))}
                                <span class="score-text">(${parseFloat(phong.danhGia).toFixed(1)})</span>
                            </div>
                        </div>
                        <p class="limit-text">
                            ${phong.moTa}
                        </p>
                    </div>
                    <div class="thong-tin-lien-lac">
                        <span class="name">${phong.tenChuTro}</span>
                        <span class="sdt">${phong.soDienThoai}</span>
                        <span class="zalo">${phong.soDienThoai}</span>
                    </div>
                </div> 
            </a>
        </article>
    </div>`;
}

// Chú thích: Hàm này sẽ định dạng tiền tệ
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount);
}

// Chú thích: Hàm này sẽ định dạng ngày tháng
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

// Chú thích: Sự kiện này sẽ xảy ra khi click bất kỳ đâu trên trang
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        if (!dropdown.contains(event.target)) {
            const content = dropdown.querySelector('[class^="dropdown_content"]');
            if (content && content.style.display === 'block') {
                content.style.display = 'none';
            }
        }
    });
});
