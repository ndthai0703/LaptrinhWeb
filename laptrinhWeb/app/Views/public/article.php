<?php require_once __DIR__ . '/layouts/header.php'; ?>


<article class="container">
    <div class="article-header">
        <a href="category.php?slug=<?= htmlspecialchars($article['category_slug']) ?>" class="article-category" style="display: inline-block; margin-bottom: 1rem;">
            <?= htmlspecialchars($article['category_name']) ?>
        </a>
        <h1><?= htmlspecialchars($article['title']) ?></h1>
        <div style="color: var(--text-muted); font-size: 1rem; margin-top: 1rem; display: flex; justify-content: center; gap: 1rem;">
            <span>Bởi <strong><?= htmlspecialchars($article['author_name']) ?></strong></span>
            <span>&bull;</span>
            <span><?= date('d/m/Y', strtotime($article['created_at'])) ?></span>
            <span>&bull;</span>
            <span><?= $article['views'] + 1 ?> Lượt xem</span>
        </div>
    </div>

    <?php if ($article['image']): ?>
        <img src="<?= $uploadPath . htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="article-hero-img">
    <?php endif; ?>

    <div class="article-body">
        <?= $article['content'] ?>
    </div>

    <!-- Phần bình luận -->
    <div class="comments-section">
        <h2 style="font-size: 1.5rem; margin-bottom: 2rem;">Bình luận (<?= count($comments) ?>)</h2>

        <?php if ($commentSuccess): ?>
            <div class="alert alert-success"><?= $commentSuccess ?></div>
        <?php endif; ?>
        <?php if ($commentError): ?>
            <div class="alert alert-danger"><?= $commentError ?></div>
        <?php endif; ?>

        <?php if (isLoggedIn()): ?>
            <div class="comment-form-container">
                <form action="" method="POST" class="comment-form">
                    <h3>Để lại ý kiến của bạn</h3>
                    <textarea name="content" placeholder="Viết bình luận của bạn tại đây..." required></textarea>
                    <button type="submit" name="submit_comment" class="btn btn-primary">Gửi bình luận</button>
                </form>
            </div>
        <?php else: ?>
            <div class="login-to-comment">
                <p>Bạn cần <strong><a href="auth/login.php?redirect=<?= urlencode($currentUrl) ?>" style="color: var(--primary); text-decoration: underline;">đăng nhập</a></strong> để có thể tham gia bình luận.</p>
            </div>
        <?php endif; ?>

        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <p style="text-align: center; color: var(--gray); padding: 2rem 0;">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item <?= !empty($comment['parent_id']) ? 'comment-reply' : '' ?>">
                        <div class="comment-avatar">
                            <?= strtoupper(substr($comment['username'], 0, 1)) ?>
                        </div>
                        <div class="comment-details">
                            <div class="comment-author"><?= htmlspecialchars($comment['username']) ?></div>
                            <div class="comment-date"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></div>
                            <div class="comment-text" id="comment-text-<?= $comment['id'] ?>">
                                <?= nl2br(htmlspecialchars($comment['content'])) ?>
                            </div>
                            <?php if (isLoggedIn()): ?>
                                <button type="button" class="comment-reply-button" onclick="toggleReply(<?= $comment['id'] ?>)">
                                    <i class="fas fa-reply"></i> Trả lời
                                </button>
                                <form method="POST" action="" id="reply-form-<?= $comment['id'] ?>" class="comment-reply-form" style="display: none;">
                                    <input type="hidden" name="action" value="reply_comment">
                                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                    <textarea name="content" placeholder="Viết câu trả lời..." required></textarea>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Gửi trả lời</button>
                                        <button type="button" class="btn btn-secondary" onclick="toggleReply(<?= $comment['id'] ?>)">Hủy</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                            <?php if (isLoggedIn() && $_SESSION['user_id'] == $comment['user_id']): ?>
                                <div style="margin-top: 10px; font-size: 0.9rem;">
                                    <button onclick="toggleEdit(<?= $comment['id'] ?>)" style="background: none; border: none; color: var(--primary); cursor: pointer; padding: 0;"><i class="fas fa-pen"></i> Sửa</button>
                                    <span style="color: #ccc; margin: 0 5px;">|</span>
                                    <form method="POST" action="" id="delete-form-<?= $comment['id'] ?>" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_comment">
                                        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                        <button type="button" onclick="confirmDelete(<?= $comment['id'] ?>)" style="background: none; border: none; color: red; cursor: pointer; padding: 0;"><i class="fas fa-trash"></i> Xóa</button>
                                    </form>
                                </div>
                                <!-- Biểu mẫu sửa bình luận -->
                                <form method="POST" action="" id="edit-form-<?= $comment['id'] ?>" style="display: none; margin-top: 10px;">
                                    <input type="hidden" name="action" value="update_comment">
                                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                    <textarea name="content" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 8px; font-family: inherit; font-size: 0.95rem; resize: vertical;" required><?= htmlspecialchars($comment['content']) ?></textarea>
                                    <div>
                                        <button type="submit" class="btn btn-primary" style="padding: 6px 16px; font-size: 0.85rem;">Lưu thay đổi</button>
                                        <button type="button" class="btn btn-secondary" style="padding: 6px 16px; font-size: 0.85rem;" onclick="toggleEdit(<?= $comment['id'] ?>)">Hủy</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid #E5E7EB; text-align: center;">
        <a href="index.php" class="btn btn-secondary">&larr; Quay lại Tin tức</a>
    </div>
</article>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleEdit(id) {
    var form = document.getElementById('edit-form-' + id);
    var text = document.getElementById('comment-text-' + id);
    if (form.style.display === 'none') {
        form.style.display = 'block';
        text.style.display = 'none';
    } else {
        form.style.display = 'none';
        text.style.display = 'block';
    }
}

function toggleReply(id) {
    var form = document.getElementById('reply-form-' + id);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    if (form.style.display === 'block') form.querySelector('textarea').focus();
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: "Bình luận này sẽ bị xóa vĩnh viễn và không thể khôi phục!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: '<i class="fas fa-trash"></i> Vâng, Xóa nó!',
        cancelButtonText: 'Hủy thao tác',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-4 shadow-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    })
}

document.addEventListener('DOMContentLoaded', function () {
    const images = Array.from(document.querySelectorAll('.article-hero-img, .article-body img'));
    if (!images.length) return;

    const lightbox = document.createElement('div');
    lightbox.className = 'article-lightbox';
    lightbox.setAttribute('role', 'dialog');
    lightbox.setAttribute('aria-modal', 'true');
    lightbox.innerHTML = '<button class="article-lightbox-close" aria-label="Đóng">&times;</button>' +
        '<button class="article-lightbox-prev" aria-label="Ảnh trước">&#10094;</button>' +
        '<img class="article-lightbox-image" alt="">' +
        '<button class="article-lightbox-next" aria-label="Ảnh tiếp theo">&#10095;</button>' +
        '<div class="article-lightbox-counter"></div>';
    document.body.appendChild(lightbox);

    const preview = lightbox.querySelector('.article-lightbox-image');
    const counter = lightbox.querySelector('.article-lightbox-counter');
    const previous = lightbox.querySelector('.article-lightbox-prev');
    const next = lightbox.querySelector('.article-lightbox-next');
    let currentIndex = 0;

    function showImage(index) {
        currentIndex = (index + images.length) % images.length;
        const image = images[currentIndex];
        preview.src = image.currentSrc || image.src;
        preview.alt = image.alt || 'Ảnh bài viết';
        counter.textContent = (currentIndex + 1) + ' / ' + images.length;
        previous.hidden = images.length < 2;
        next.hidden = images.length < 2;
    }

    function closeLightbox() {
        lightbox.classList.remove('is-visible');
        document.body.classList.remove('lightbox-open');
    }

    images.forEach(function (image, index) {
        image.classList.add('article-clickable-image');
        image.setAttribute('tabindex', '0');
        image.addEventListener('click', function () {
            showImage(index);
            lightbox.classList.add('is-visible');
            document.body.classList.add('lightbox-open');
        });
        image.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                image.click();
            }
        });
    });

    lightbox.querySelector('.article-lightbox-close').addEventListener('click', closeLightbox);
    previous.addEventListener('click', function () { showImage(currentIndex - 1); });
    next.addEventListener('click', function () { showImage(currentIndex + 1); });
    lightbox.addEventListener('click', function (event) {
        if (event.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', function (event) {
        if (!lightbox.classList.contains('is-visible')) return;
        if (event.key === 'Escape') closeLightbox();
        if (event.key === 'ArrowLeft') showImage(currentIndex - 1);
        if (event.key === 'ArrowRight') showImage(currentIndex + 1);
    });
});
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
