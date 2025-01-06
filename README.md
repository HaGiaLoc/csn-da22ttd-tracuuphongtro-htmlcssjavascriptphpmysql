# ỨNG DỤNG WEB TRA CỨU DỊCH VỤ PHÒNG TRỌ TẠI THÀNH PHỐ TRÀ VINH

## Mục lục
- [ỨNG DỤNG WEB TRA CỨU DỊCH VỤ PHÒNG TRỌ TẠI THÀNH PHỐ TRÀ VINH](#ứng-dụng-web-tra-cứu-dịch-vụ-phòng-trọ-tại-thành-phố-trà-vinh)
  - [Mục lục](#mục-lục)
  - [Giới thiệu](#giới-thiệu)
  - [Tính năng](#tính-năng)
  - [Giới Thiệu Về Repositories của tôi](#giới-thiệu-về-repositories-của-tôi)
  - [Cài đặt](#cài-đặt)
  - [Sử dụng](#sử-dụng)
  - [Giấy phép](#giấy-phép)

---

## Giới thiệu
Ứng dụng web này được phát triển để giúp người dùng tra cứu thông tin về các dịch vụ phòng trọ tại Thành phố Trà Vinh. Người dùng có thể tìm kiếm, xem chi tiết và đánh giá các phòng trọ có sẵn.

---

## Tính năng
- **Tìm kiếm phòng trọ**: Người dùng có thể tìm kiếm phòng trọ theo địa chỉ, giá cả và diện tích.
- **Xem chi tiết phòng trọ**: Cung cấp thông tin chi tiết về từng phòng trọ, bao gồm giá cả, diện tích, địa chỉ, vị trí trên Google Maps và thông tin liên hệ của chủ trọ.
- **Đánh giá và nhận xét**: Người dùng có thể để lại đánh giá và nhận xét về các phòng trọ mà họ đã thuê.
- **Quản lý cho quản trị viên**: Quản trị viên có thể thêm, sửa, xóa thông tin phòng trọ, người dùng và thêm, xóa dịch vụ.

---

## Giới Thiệu Về Repositories của tôi
Đây là nơi tôi để toàn bộ thư mục liên quan đến source code của ứng dụng web và các file báo cáo. Toàn bộ thư mục nằm ở branches main trong repositories này. Dưới đây là miêu tả chi tiết:
- **src:** Đây là nơi lưu source code liên quan đến ứng dụng web và cơ sở dữ liệu.
- **thesis:** Đây là nơi chứa các tập tin tài liệu văn bản của Đồ án.

--- 

## Cài đặt
1. **Tải về ứng dụng web**:
   - Ứng dụng web có thể được tải về bằng cách tải file src.

2. **Cài đặt XAMPP**:
   - Đảm bảo rằng [XAMPP](https://www.apachefriends.org) đã được cài đặt trên máy tính của bạn.
   - Sau khi cài đặt, hãy khởi động Apache và MySQL.
   - Tiếp theo, hãy tạo một cơ sở dữ liệu trong MySQL có tên là `phongtro_db`. Sau đó, hãy nhập các bảng vào cơ sở dữ liệu này từ file `phongtro_db.sql`.

3. **Cài đặt ứng dụng web**:
   - Vào thư mục htdocs trong xampp `.\xampp\htdocs\`.
   - Tạo một thư mục với cái tên bất kỳ, ví dụ `phongtro`.

4. **Chạy ứng dụng**:
   - Mở trình duyệt và truy cập vào `http://localhost/phongtro/src/website/index.php`.

---

## Sử dụng
- Truy cập vào trang chủ để tra cứu phòng trọ.
- Sử dụng các bộ lọc để thu hẹp kết quả tìm kiếm.
- Nhấp vào nút đăng nhập trên thanh điều hướng để đăng nhập/đăng ký.
- Nhấp vào một phòng trọ để xem thông tin chi tiết và để lại đánh giá (Nếu đã đăng nhập và tải lại trang).

---

## Giấy phép
Đây là một ứng dụng web được tạo ra chỉ vì đồ án cơ sở ngành không vì bất kỳ lợi ích thương mại hay kiếm tiền.