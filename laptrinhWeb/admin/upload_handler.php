<?php
require_once __DIR__ . '/../app/Helpers/functions.php';

// Đảm bảo người dùng là admin
if (!isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$uploadDir = '../assets/images/articles/';

// Đảm bảo thư mục upload tồn tại
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Cho phép xóa những ảnh vừa đưa vào thư viện nếu admin upload nhầm.
if (($_POST['action'] ?? '') === 'delete') {
    $filename = basename((string)($_POST['filename'] ?? ''));
    if (!$filename || !preg_match('/^[A-Za-z0-9._-]+$/', $filename)) {
        header('HTTP/1.1 400 Bad Request');
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Tên tệp không hợp lệ.']);
        exit;
    }

    $filePath = $uploadDir . $filename;
    if (is_file($filePath) && unlink($filePath)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        header('HTTP/1.1 404 Not Found');
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Không tìm thấy ảnh để xóa.']);
    }
    exit;
}

// TinyMCE gửi một file ở trường `file`, còn thư viện ảnh gửi nhiều file ở `files[]`.
$files = [];
if (isset($_FILES['file'])) {
    $files[] = $_FILES['file'];
} elseif (isset($_FILES['files'])) {
    foreach ($_FILES['files']['name'] as $index => $name) {
        $files[] = [
            'name' => $name,
            'tmp_name' => $_FILES['files']['tmp_name'][$index],
            'error' => $_FILES['files']['error'][$index],
        ];
    }
}

if (!$files) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Không có tệp nào được tải lên.']);
    exit;
}

$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$scriptPath = $_SERVER['SCRIPT_NAME'];
$rootPath = dirname(dirname($scriptPath));
$locations = [];

foreach ($files as $file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Tải lên thất bại với mã lỗi ' . $file['error']]);
        exit;
    }

    $ext = strtolower(pathinfo(basename($file['name']), PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Định dạng ảnh không hợp lệ: ' . htmlspecialchars($file['name'])]);
        exit;
    }

    $newName = time() . '_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Không thể di chuyển tệp đã tải lên.']);
        exit;
    }
    $locations[] = $rootPath . '/assets/images/articles/' . $newName;
}

header('Content-Type: application/json');
echo json_encode(count($locations) === 1 ? ['location' => $locations[0]] : ['locations' => $locations]);
