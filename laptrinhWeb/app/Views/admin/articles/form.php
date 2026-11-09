<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Tiêu đề trang -->
<div class="admin-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold m-0"><?= $action == 'create' ? 'Viết Bài mới' : 'Cập nhật Bài viết' ?></h1>
    <a href="articles.php" class="btn btn-light border rounded-pill px-4">
        <i class="fas fa-arrow-left me-2"></i>Quay lại Danh sách
    </a>
</div>

<!-- Thông báo -->
<?php if ($error): ?><div class="alert alert-danger border-0 shadow-sm rounded-4 animate__animated animate__shakeX"><?= $error ?></div><?php endif; ?>

<!-- Trình soạn thảo TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: 'textarea[name="content"]',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 600,
    images_upload_url: 'upload_handler.php',
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    branding: false,
    promotion: false,
    setup: function (editor) {
        window.articleEditor = editor;
        editor.on('drop', function (event) {
            const html = event.dataTransfer && event.dataTransfer.getData('text/html');
            const imageUrl = event.dataTransfer && event.dataTransfer.getData('text/uri-list');
            if (html || imageUrl) {
                event.preventDefault();
                editor.insertContent(html || '<img src="' + imageUrl + '" />');
            }
        });
    },
    content_style: "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'); *, *::before, *::after { box-sizing: border-box; } body { font-family: 'Inter', sans-serif; font-size: 1.125rem; line-height: 1.8; color: #1F2937; padding: 1.5rem; overflow-x: hidden; } p { margin-bottom: 1.5rem; } h2, h3 { margin-top: 2rem; margin-bottom: 1rem; color: #111827; font-weight: 700; } img, figure { max-width: 100% !important; height: auto !important; display: block; margin: 0 auto 1.5rem auto; border-radius: 8px; } iframe { max-width: 100%; border-radius: 8px; }"
});
</script>

<div class="admin-card border-0 shadow-sm rounded-4" style="padding:2.5rem; background: #fff;">
    <form method="POST" action="articles.php?action=<?= $action ?><?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" enctype="multipart/form-data">
        <div class="row g-5">
            
            <!-- Cột chính -->
            <div class="col-xl-8 col-lg-7">
                <div class="form-group mb-4">
                    <label class="fw-bold mb-2 text-dark fs-5"><i class="fas fa-heading text-primary me-2"></i>Tiêu đề bài viết</label>
                    <input type="text" name="title" class="form-control rounded-3 border-0 shadow-sm"
                           placeholder="Nhập tiêu đề thật hấp dẫn..."
                           value="<?= htmlspecialchars($article['title'] ?? '') ?>" required
                           style="background: #f8fafc; font-size: 1.15rem; padding: 1rem 1.25rem;">
                </div>

                <div class="form-group mb-4">
                    <label class="fw-bold mb-2 text-dark fs-5"><i class="fas fa-align-left text-info me-2"></i>Tóm tắt (Sapo)</label>
                    <textarea name="summary" class="form-control rounded-3 border-0 shadow-sm" rows="3" required
                              placeholder="Viết một đoạn ngắn tóm tắt nội dung bài viết..."
                              style="background: #f8fafc; font-size: 1.05rem; padding: 1rem 1.25rem; resize: vertical;"><?= htmlspecialchars($article['summary'] ?? '') ?></textarea>
                </div>

                <div class="form-group mb-4">
                    <label class="fw-bold mb-3 text-dark fs-5"><i class="fas fa-pen-nib text-success me-2"></i>Nội dung chi tiết</label>
                    <div class="shadow-sm rounded-3 overflow-hidden border-0">
                        <textarea name="content" class="form-control" rows="15"><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
                    </div>

                    <div class="article-image-library mt-4" id="articleImageLibrary">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <label class="fw-bold mb-1 text-dark fs-5"><i class="fas fa-images text-primary me-2"></i>Thư viện ảnh bài viết</label>
                                <p class="text-muted mb-0 small">Tải nhiều ảnh một lần, sau đó kéo ảnh vào vị trí bất kỳ trong bài viết.</p>
                            </div>
                            <span class="badge bg-light text-secondary border" id="imageCount">0 ảnh</span>
                        </div>
                        <label class="article-upload-zone" id="articleUploadZone" for="articleImages">
                            <i class="fas fa-cloud-arrow-up"></i>
                            <strong>Chọn nhiều ảnh hoặc kéo thả vào đây</strong>
                            <small>JPG, PNG, GIF, WEBP · Mỗi ảnh tối đa theo cấu hình máy chủ</small>
                        </label>
                        <input type="file" id="articleImages" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                        <div class="article-image-grid" id="articleImageGrid"></div>
                        <div class="small text-danger mt-2 d-none" id="imageUploadError"></div>
                    </div>
                </div>
            </div>

            <!-- Thanh bên -->
            <div class="col-xl-4 col-lg-5">
                <div class="rounded-4 p-4 shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <h5 class="fw-bold mb-4 text-dark" style="font-size: 1.2rem;"><i class="fas fa-cog text-secondary me-2"></i>Cài đặt xuất bản</h5>

                    <div class="form-group mb-4">
                        <label class="fw-semibold mb-2" style="color: #475569;">Danh mục</label>
                        <select name="category_id" class="form-select rounded-3 border-0 shadow-sm" style="padding: 0.8rem 1rem; cursor: pointer; font-size: 1rem;">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                        <?= (isset($article['category_id']) && $article['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="fw-semibold mb-2" style="color: #475569;">Trạng thái</label>
                        <select name="status" class="form-select rounded-3 border-0 shadow-sm" style="padding: 0.8rem 1rem; cursor: pointer; font-size: 1rem;">
                            <option value="published" <?= (isset($article['status']) && $article['status'] == 'published') ? 'selected' : '' ?>>🌐 Đã xuất bản</option>
                            <option value="draft"     <?= (isset($article['status']) && $article['status'] == 'draft')     ? 'selected' : '' ?>>📝 Bản nháp</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="fw-semibold mb-2" style="color: #475569;">Ảnh đại diện</label>
                        <?php if (!empty($article['image'])): ?>
                            <div class="mb-3 text-center position-relative">
                                <img src="../assets/images/articles/<?= htmlspecialchars($article['image']) ?>"
                                     alt="Ảnh hiện tại"
                                     class="img-fluid rounded-3 shadow-sm w-100"
                                     style="max-height: 220px; object-fit: cover;">
                                <input type="hidden" name="current_image"
                                       value="<?= htmlspecialchars($article['image']) ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="position-relative">
                            <input type="file" name="image" class="form-control rounded-3 border-0 shadow-sm"
                                   accept="image/jpeg,image/png,image/gif,image/webp" style="padding: 0.7rem 1rem;">
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size: 0.85rem;"><i class="fas fa-info-circle me-1"></i>Định dạng: JPG, PNG, WEBP</small>
                    </div>

                    <hr class="my-4" style="border-color: #cbd5e1;">

                    <button type="submit" class="btn btn-primary w-100 rounded-pill shadow d-flex align-items-center justify-content-center"
                            style="padding: 0.8rem; font-weight: 500; transition: all 0.3s ease;">
                        <i class="fas fa-paper-plane me-2"></i>
                        <?= $action == 'create' ? 'Đăng bài ngay' : 'Cập nhật bài viết' ?>
                    </button>
                    
                    <a href="articles.php" class="btn btn-light w-100 rounded-pill mt-3 shadow-sm d-flex align-items-center justify-content-center" style="padding: 0.8rem; font-weight: 500;">
                        <i class="fas fa-arrow-left me-2"></i>Hủy thao tác
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
(function () {
    const input = document.getElementById('articleImages');
    const zone = document.getElementById('articleUploadZone');
    const grid = document.getElementById('articleImageGrid');
    const count = document.getElementById('imageCount');
    const errorBox = document.getElementById('imageUploadError');
    if (!input || !zone || !grid) return;

    let imageTotal = 0;

    function setError(message) {
        errorBox.textContent = message || '';
        errorBox.classList.toggle('d-none', !message);
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>'"]/g, function (character) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'}[character];
        });
    }

    function insertImage(url, name) {
        const editor = window.articleEditor || tinymce.activeEditor;
        if (!editor) return;
        editor.focus();
        editor.insertContent('<p><img src="' + escapeHtml(url) + '" alt="' + escapeHtml(name || 'Ảnh bài viết') + '" /></p>');
    }

    async function deleteImage(card, url) {
        if (!window.confirm('Bạn có chắc muốn xóa ảnh này khỏi thư viện không?')) return;
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('filename', url.split('/').pop());
        card.classList.add('is-deleting');
        try {
            const response = await fetch('upload_handler.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.error || 'Không thể xóa ảnh.');
            card.remove();
            imageTotal -= 1;
            count.textContent = imageTotal + ' ảnh';
        } catch (error) {
            card.classList.remove('is-deleting');
            setError(error.message);
        }
    }

    function addImageCard(url, name) {
        imageTotal += 1;
        count.textContent = imageTotal + ' ảnh';
        const card = document.createElement('div');
        card.className = 'article-image-card';
        card.draggable = true;
        card.innerHTML = '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(name) + '">' +
            '<button type="button" class="article-image-delete" title="Xóa ảnh" aria-label="Xóa ảnh"><i class="fas fa-xmark"></i></button>' +
            '<div class="article-image-card-footer"><span title="' + escapeHtml(name) + '">' + escapeHtml(name) + '</span>' +
            '<button type="button" class="btn btn-sm btn-primary rounded-pill">Chèn</button></div>';
        card.addEventListener('dragstart', function (event) {
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/html', '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(name) + '">');
            event.dataTransfer.setData('text/uri-list', url);
            card.classList.add('is-dragging');
        });
        card.addEventListener('dragend', function () { card.classList.remove('is-dragging'); });
        card.querySelector('.article-image-card-footer button').addEventListener('click', function () { insertImage(url, name); });
        card.querySelector('.article-image-delete').addEventListener('click', function () { deleteImage(card, url); });
        grid.appendChild(card);
    }

    async function uploadImages(files) {
        if (!files.length) return;
        setError('');
        zone.classList.add('is-uploading');
        const formData = new FormData();
        files.forEach(function (file) { formData.append('files[]', file); });
        try {
            const response = await fetch('upload_handler.php', { method: 'POST', body: formData });
            const result = await response.json();
            const locations = result.locations || (result.location ? [result.location] : []);
            if (!response.ok || !locations.length) throw new Error(result.error || 'Tải ảnh lên thất bại.');
            locations.forEach(function (url, index) { addImageCard(url, files[index] ? files[index].name : 'Ảnh bài viết'); });
        } catch (error) {
            setError(error.message);
        } finally {
            zone.classList.remove('is-uploading');
            input.value = '';
        }
    }

    input.addEventListener('change', function () { uploadImages(Array.from(input.files)); });
    ['dragenter', 'dragover'].forEach(function (type) {
        zone.addEventListener(type, function (event) { event.preventDefault(); zone.classList.add('is-dragover'); });
    });
    ['dragleave', 'drop'].forEach(function (type) {
        zone.addEventListener(type, function (event) {
            event.preventDefault();
            zone.classList.remove('is-dragover');
            if (type === 'drop') uploadImages(Array.from(event.dataTransfer.files || []));
        });
    });
})();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
