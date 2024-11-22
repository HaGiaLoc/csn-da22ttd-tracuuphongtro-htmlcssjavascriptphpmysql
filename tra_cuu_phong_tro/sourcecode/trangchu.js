window.onload = function() {
    updateSliderLimits();
    updateInput();
};

document.addEventListener('DOMContentLoaded', function() {
    const lien_ket_dieu_huong = document.querySelectorAll('nav a');

    lien_ket_dieu_huong.forEach(lien_ket => {
        lien_ket.addEventListener('click', function() {
            lien_ket_dieu_huong.forEach(lien_ket => lien_ket.classList.remove('active'));

            this.classList.add('active');
        });
    });

    const dropdownItems = document.querySelectorAll('.dropdown_content-location div, .dropdown_content-price div, .dropdown_content-area div');
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(event) {
            event.stopPropagation();
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

    maxPriceInput.value = slider.value;

    const percentage = (slider.value - slider.min) / (slider.max - slider.min) * 100;

    slider.style.background = `linear-gradient(to right, #33CCFF ${percentage}%, #333 ${percentage}%)`;
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

function selectDistrict(dropdown) {
    const value = dropdown.value;

    const hamlets = document.querySelectorAll('.dropdown_content-hamlet');
    const labels = document.querySelectorAll('label[for="dropdown_content-hamlet"]');
    hamlets.forEach(hamlet => hamlet.style.display = 'none');
    labels.forEach(label => label.style.display = 'none');

    switch (value) {
        case 'tp-travinh':
            document.getElementById('TraVinh').style.display = 'block';
            document.getElementById('label-TraVinh').style.display = 'block';
            break;
        case 'tx-duyenhai':
            document.getElementById('tx_DuyenHai').style.display = 'block';
            document.getElementById('label-tx_DuyenHai').style.display = 'block';
            break;
        case 'canglong':
            document.getElementById('CangLong').style.display = 'block';
            document.getElementById('label-CangLong').style.display = 'block';
        case 'cauke':
            document.getElementById('CauKe').style.display = 'block';
            document.getElementById('label-CauKe').style.display = 'block';
        case 'caungang':
            document.getElementById('CauNgang').style.display = 'block';
            document.getElementById('label-CauNgang').style.display = 'block';
        case 'h-duyenhai':
            document.getElementById('h_DuyenHai').style.display = 'block';
            document.getElementById('label-h_DuyenHai').style.display = 'block';
        case 'chauthanh':
            document.getElementById('ChauThanh').style.display = 'block';
            document.getElementById('label-ChauThanh').style.display = 'block';
        case 'tracu':
            document.getElementById('TraCu').style.display = 'block';
            document.getElementById('label-TraCu').style.display = 'block';
        case 'tieucan':
            document.getElementById('TieuCan').style.display = 'block';
            document.getElementById('label-TieuCan').style.display = 'block';
        default:
            console.log('Không có xã/phường tương ứng!');
    }
}

document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const content = dropdown.querySelector('.dropdown_content-location, .dropdown_content-price, .dropdown_content-area');
        if (content.style.display === "block" && !dropdown.contains(event.target)) {
            content.style.display = "none";
        }
    });
});
