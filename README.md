# NewsDaily – Website tin tức

Website tin tức được xây dựng bằng PHP thuần theo mô hình MVC đơn giản. Hệ thống cho phép độc giả xem tin, lọc theo danh mục, tìm kiếm bài viết, đăng ký/đăng nhập và bình luận. Quản trị viên có thể quản lý bài viết, danh mục, người dùng, hình ảnh và theo dõi số liệu tổng quan trên dashboard.

## 1. Chức năng chính

### Khu vực công khai

- Trang chủ hiển thị các bài viết mới nhất.
- Hiển thị danh sách danh mục tin tức.
- Xem bài viết theo slug và tăng lượt xem khi mở bài.
- Xem bài viết theo danh mục.
- Tìm kiếm bài viết theo từ khóa trong tiêu đề.
- Đăng ký tài khoản người dùng.
- Đăng nhập và đăng xuất.
- Bình luận bài viết khi đã đăng nhập.
- Sửa hoặc xóa bình luận của chính người dùng.
- Hỗ trợ trả lời bình luận thông qua `parent_id`.
- Hiển thị ảnh đại diện bài viết và nội dung HTML được biên tập.

### Khu vực quản trị

- Đăng nhập riêng tại khu vực Admin.
- Dashboard thống kê số lượng người dùng, danh mục và bài viết.
- Theo dõi tăng trưởng bài viết/người dùng theo ngày và theo tháng.
- Xem phân bố bài viết theo danh mục và các bài viết có nhiều lượt xem.
- Quản lý bài viết: thêm, sửa, xóa, tìm kiếm, lọc theo danh mục/trạng thái và phân trang.
- Quản lý danh mục: thêm, sửa, xóa, tìm kiếm và phân trang.
- Không cho xóa danh mục đang có bài viết.
- Quản lý người dùng: thêm, sửa, xóa, phân quyền `user`/`admin` và phân trang.
- Upload nhiều định dạng ảnh: `jpg`, `jpeg`, `png`, `gif`, `webp`.
- Tự động xóa ảnh cũ khi thay ảnh hoặc xóa bài viết.

## 2. Công nghệ sử dụng

- PHP 8.2 trở lên
- MySQL hoặc MariaDB
- PDO với prepared statements
- HTML5, CSS3, JavaScript
- Bootstrap 5 (khu vực quản trị)
- Font Awesome
- TinyMCE/CDN và Google Fonts được gọi từ CDN trong giao diện
- Apache thông qua XAMPP/WAMP/Laragon hoặc PHP Built-in Server

File SQL được xuất từ phpMyAdmin với môi trường tham chiếu PHP `8.2.12` và MariaDB `10.4.32`.

## 3. Cấu trúc thư mục

```text
laptrinhWeb/
├── admin/                       # Các entry point của khu vực quản trị
│   ├── articles.php             # Quản lý bài viết
│   ├── categories.php           # Quản lý danh mục
│   ├── index.php                # Dashboard
│   ├── login.php                # Đăng nhập Admin
│   ├── upload_handler.php       # API upload/xóa ảnh
│   └── users.php                # Quản lý người dùng
├── app/
│   ├── Controllers/             # Controller công khai và Admin
│   ├── Core/                    # Kết nối CSDL
│   ├── Helpers/                 # Autoload, session, helper dùng chung
│   ├── Models/                  # Truy vấn và nghiệp vụ dữ liệu
│   └── Views/                   # Giao diện public, auth và admin
├── assets/
│   ├── css/                     # style.css, admin.css
│   └── images/articles/         # Ảnh bài viết được upload
├── auth/
│   ├── login.php                # Đăng nhập người dùng
│   ├── register.php             # Đăng ký người dùng
│   └── logout.php               # Đăng xuất
├── config/database.php          # Cấu hình kết nối database
├── article.php                  # Entry point xem bài viết
├── category.php                 # Entry point xem danh mục
├── index.php                    # Entry point trang chủ
├── search.php                   # Entry point tìm kiếm
├── add_sample.php               # Script thêm dữ liệu mẫu
├── reset.php                    # Script đặt lại mật khẩu admin
└── news_db.sql                  # Schema và dữ liệu mẫu
```

Ứng dụng tự nạp các class namespace `App\` bằng autoloader trong `app/Helpers/functions.php`, sau đó điều phối request qua Controller → Model → View.

## 4. Cài đặt

### Bước 1: Đặt source vào web server

Copy thư mục dự án vào thư mục web root, ví dụ:

```text
C:\xampp\htdocs\laptrinhWeb
```

Nếu dùng Laragon, có thể đặt trong thư mục `www`.

### Bước 2: Tạo database

Tạo database có tên `news_db`, sử dụng mã hóa `utf8mb4`, sau đó import file [`news_db.sql`](./news_db.sql).

Ví dụ với MySQL/MariaDB command line:

```bash
mysql -u root -p -e "CREATE DATABASE news_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p news_db < news_db.sql
```

Hoặc import trực tiếp bằng phpMyAdmin.

### Bước 3: Cấu hình kết nối

Mở [`config/database.php`](./config/database.php) và cập nhật thông tin cho môi trường của bạn:

```php
return [
    'host'    => 'localhost',
    'dbname'  => 'news_db',
    'user'    => 'root',
    'pass'    => '',
    'charset' => 'utf8mb4'
];
```

Đảm bảo PHP đã bật extension `pdo_mysql`.

### Bước 4: Kiểm tra quyền thư mục upload

Thư mục [`assets/images/articles/`](./assets/images/articles/) phải tồn tại và có quyền ghi để Admin có thể upload ảnh. Ứng dụng cũng cố gắng tự tạo thư mục này khi cần.

## 5. Chạy dự án

### Với XAMPP/WAMP/Laragon

1. Khởi động Apache và MySQL/MariaDB.
2. Truy cập:

```text
http://localhost/laptrinhWeb/
```

### Với PHP Built-in Server

Chạy tại thư mục gốc dự án:

```bash
php -S localhost:8000
```

Sau đó mở:

```text
http://localhost:8000/
```

## 6. Các URL quan trọng

| Chức năng | URL mặc định |
|---|---|
| Trang chủ | `/index.php` |
| Xem bài viết | `/article.php?slug=<slug-bai-viet>` |
| Xem danh mục | `/category.php?slug=<slug-danh-muc>` |
| Tìm kiếm | `/search.php?search=<tu-khoa>` |
| Đăng nhập | `/auth/login.php` |
| Đăng ký | `/auth/register.php` |
| Đăng nhập Admin | `/admin/login.php` |
| Dashboard Admin | `/admin/index.php` |
| Quản lý bài viết | `/admin/articles.php` |
| Quản lý danh mục | `/admin/categories.php` |
| Quản lý người dùng | `/admin/users.php` |

## 7. Tài khoản mẫu

Database mẫu có sẵn tài khoản `admin` với quyền Admin. Nếu không đăng nhập được sau khi import database, có thể mở [`reset.php`](./reset.php) một lần để đặt lại:

```text
Username: admin
Password: admin123
```

Sau khi đặt lại mật khẩu thành công, cần xóa hoặc đổi tên [`reset.php`](./reset.php) để tránh người khác gọi lại script này trên môi trường production.

Người dùng mới đăng ký luôn được tạo với role `user`. Chỉ tài khoản có role `admin` mới truy cập được khu vực quản trị.

## 8. Cấu trúc cơ sở dữ liệu

Database gồm 4 bảng chính:

| Bảng | Mục đích | Trường tiêu biểu |
|---|---|---|
| `users` | Tài khoản và phân quyền | `username`, `email`, `password`, `role` |
| `categories` | Danh mục tin tức | `name`, `slug`, `description` |
| `articles` | Nội dung bài viết | `title`, `slug`, `summary`, `content`, `image`, `views`, `status` |
| `comments` | Bình luận bài viết | `article_id`, `user_id`, `parent_id`, `content` |

Quan hệ dữ liệu:

```text
users 1 ─── n articles
users 1 ─── n comments
categories 1 ─── n articles
articles 1 ─── n comments
```

Các khóa ngoại dùng `ON DELETE CASCADE` cho bài viết theo danh mục/tác giả và bình luận theo bài viết/người dùng.

## 9. Luồng xử lý tổng quát

```text
Request PHP entry point
        ↓
Controller
        ↓
Model ─── PDO ─── MySQL/MariaDB
        ↓
View
        ↓
HTML response
```

- Kết nối database dùng Singleton trong `app/Core/Database.php`.
- Mật khẩu được băm bằng `password_hash()` và kiểm tra bằng `password_verify()`.
- Các truy vấn dữ liệu chính sử dụng prepared statements.
- Session dùng để lưu trạng thái đăng nhập và role người dùng.
- Bài viết có hai trạng thái: `draft` và `published`; giao diện công khai chỉ hiển thị bài `published`.

## 10. Lưu ý triển khai và bảo mật

- Không đưa mật khẩu database thật lên Git hoặc môi trường công khai.
- Nên xóa [`reset.php`](./reset.php) và [`add_sample.php`](./add_sample.php) sau khi khởi tạo dữ liệu.
- Nên tắt hiển thị lỗi PHP trên production và ghi lỗi vào log riêng.
- Chỉ cho phép upload ảnh, giới hạn dung lượng file và kiểm tra MIME type ở tầng server khi triển khai thực tế.
- Nên bổ sung CSRF token cho các form POST và xác nhận trước các thao tác xóa.
- Nội dung bài viết hỗ trợ HTML nên cần lọc/sanitize HTML trước khi cho phép nhiều nhóm người dùng biên tập.
- CDN Bootstrap, Font Awesome, Google Fonts và các tài nguyên ngoài cần có kết nối Internet khi tải giao diện đầy đủ.
- Nếu đặt dự án trong thư mục con khác `laptrinhWeb`, kiểm tra lại các đường dẫn tương đối trong các view và file Admin.

## 11. Khắc phục lỗi thường gặp

### Lỗi kết nối database

- Kiểm tra MySQL/MariaDB đã chạy chưa.
- Kiểm tra database `news_db` đã được tạo và import chưa.
- Kiểm tra `host`, `dbname`, `user`, `pass` trong [`config/database.php`](./config/database.php).
- Kiểm tra PHP đã bật `pdo_mysql`.

### Không upload được ảnh

- Kiểm tra thư mục [`assets/images/articles/`](./assets/images/articles/) tồn tại.
- Cấp quyền ghi cho thư mục.
- Kiểm tra `upload_max_filesize` và `post_max_size` trong `php.ini`.
- Chỉ sử dụng các định dạng `jpg`, `jpeg`, `png`, `gif`, `webp`.

### Không vào được Admin

- Đăng nhập tại `/admin/login.php` bằng tài khoản có role `admin`.
- Nếu quên mật khẩu, chạy [`reset.php`](./reset.php) một lần rồi xóa file này.
- Kiểm tra session và cookie của trình duyệt.

## 12. Hướng phát triển

- Bổ sung CSRF protection và phân quyền chi tiết hơn.
- Thêm validate MIME type, giới hạn kích thước và đổi tên ảnh an toàn.
- Thêm sitemap, meta description, Open Graph và tối ưu SEO.
- Tách cấu hình môi trường khỏi mã nguồn bằng file `.env`.
- Bổ sung test cho Model/Controller và logging tập trung.
- Cải thiện tìm kiếm bằng full-text index khi số lượng bài viết tăng.

