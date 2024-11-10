// Khởi tạo giá trị của slider và ô nhập khi trang được tải
window.onload = function() {
    updateSliderLimits();
    updateInput();
};

document.addEventListener('DOMContentLoaded', function() {
    // Lấy các liên kết điều hướng
    const lien_ket_dieu_huong = document.querySelectorAll('nav a');

    // Thêm sự kiện click cho từng liên kết
    lien_ket_dieu_huong.forEach(lien_ket => {
        lien_ket.addEventListener('click', function() {
            // Xóa lớp 'active' khỏi tất cả các liên kết
            lien_ket_dieu_huong.forEach(lien_ket => lien_ket.classList.remove('active'));

            // Thêm lớp 'active' cho liên kết được nhấn
            this.classList.add('active');
        });
    });
    // Thêm sự kiện cho các phần tử trong dropdown
    const dropdownItems = document.querySelectorAll('.dropdown_content-location div, .dropdown_content-price div, .dropdown_content-area div');
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(event) {
            event.stopPropagation(); // Ngăn chặn sự kiện click lan ra ngoài
            // Xử lý logic chọn phần tử ở đây
        });
    });
});

function updateSliderLimits() {
    const minPriceInput = document.getElementById('gia_toi_thieu');
    const maxPriceInput = document.getElementById('gia_toi_da');
    const slider = document.getElementById('slider_gia');

    const minPrice = parseInt(minPriceInput.value);
    const maxPrice = parseInt(maxPriceInput.value);

    slider.min = minPrice;
    slider.max = maxPrice;

    if (slider.value < minPrice) {
        slider.value = minPrice;
    }

    if (slider.value > maxPrice) {
        slider.value = maxPrice;
    }

    updatePriceInputs();
}

function updatePriceInputs() {
    const slider = document.getElementById('slider_gia');
    const maxPriceInput = document.getElementById('gia_toi_da');

    // Cập nhật giá trị tối đa từ slider
    maxPriceInput.value = slider.value;
}

function toggleDropdown(dropdown) {
    const content = dropdown.querySelector('.dropdown_content-location, .dropdown_content-price, .dropdown_content-area');
    const allContents = document.querySelectorAll('.dropdown_content-location, .dropdown_content-price, .dropdown_content-area');
    
    allContents.forEach(item => {
        if (item !== content) {
            item.style.display = "none";
        }
    });

    content.style.display = content.style.display === "none" || content.style.display === "" ? "block" : "none";
}


// Thêm sự kiện click cho document để đóng dropdown khi nhấn bên ngoài
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const content = dropdown.querySelector('.dropdown_content-location, .dropdown_content-price, .dropdown_content-area');
        if (content.style.display === "block" && !dropdown.contains(event.target)) {
            content.style.display = "none";
        }
    });
});
