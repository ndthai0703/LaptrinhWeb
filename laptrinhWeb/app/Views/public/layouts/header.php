<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NewsDaily - Modern News Portal</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-top">
        <div class="container">
            <div class="nav-left">
                <a href="index.php" class="navbar-brand">BÁO HƠI MỚI</a>

                <div class="weather-widget">
                    <div class="location-info">
                        <span class="city">TP HCM</span>
                        <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                        <div class="location-dropdown">
                            <div class="dropdown-search">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Tìm địa điểm" id="city-search-input">
                            </div>
                            <div class="city-list-wrapper">
                                <div class="city-list" id="full-city-list">
                                    <div class="loading-cities" style="padding: 10px; font-size: 0.8rem; color: #94a3b8;">
                                        <i class="fas fa-spinner fa-spin"></i> Đang tải danh sách...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="weather-detail">
                        <i class="fas fa-cloud-sun"></i>
                        <span class="temp">34°</span>
                    </div>
                    <div class="current-date">
                        <?php
                        $days = ["Chủ nhật","Thứ hai","Thứ ba","Thứ tư","Thứ năm","Thứ sáu","Thứ bảy"];
                        echo $days[date('w')] . ", " . date('d/m/Y');
                        ?>
                    </div>
                </div>
            </div>

            <form action="search.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Tìm kiếm tin tức..." required>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>

            <div class="nav-auth">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin/index.php" class="admin-link">Trang quản trị</a>
                    <?php endif; ?>
                    <a href="auth/logout.php" class="logout-link">Đăng xuất</a>
                <?php else: ?>
                    <a href="auth/login.php" class="login-link">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="navbar-bottom" id="navMenu">
        <div class="container">
            <div class="category-nav">
                <a href="index.php" class="nav-home"><i class="fas fa-home"></i></a>
                <button type="button" class="nav-scroll-btn nav-scroll-prev" id="navScrollPrev" aria-label="Danh mục trước">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="nav-cats" id="categoryNav">
                    <?php
                    use App\Models\CategoryModel;
                    $categoryModel  = new CategoryModel();
                    $allCategories  = $categoryModel->getAllCategories();
                    ?>
                    <?php foreach ($allCategories as $cat): ?>
                        <a href="category.php?slug=<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?></a>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="nav-scroll-btn nav-scroll-next" id="navScrollNext" aria-label="Danh mục tiếp theo">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <?php if (!empty($allCategories)): ?>
                    <div class="nav-dropdown">
                        <input type="checkbox" id="nav-dropdown-check" class="nav-dropdown-check">
                        <label for="nav-dropdown-check" class="nav-dropdown-toggle" aria-label="Mở danh sách danh mục">
                            <i class="fas fa-bars"></i>
                        </label>
                        <div class="nav-dropdown-menu">
                            <?php foreach ($allCategories as $cat): ?>
                                <a href="category.php?slug=<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const nav = document.getElementById('categoryNav');
    const previous = document.getElementById('navScrollPrev');
    const next = document.getElementById('navScrollNext');
    const categoryDropdown = document.querySelector('.nav-dropdown');
    const categoryDropdownCheck = document.getElementById('nav-dropdown-check');
    if (!nav || !previous || !next) return;

    function updateCategoryArrows() {
        const hasOverflow = nav.scrollWidth > nav.clientWidth + 2;
        previous.disabled = !hasOverflow || nav.scrollLeft <= 2;
        next.disabled = !hasOverflow || nav.scrollLeft + nav.clientWidth >= nav.scrollWidth - 2;
        previous.classList.toggle('is-hidden', !hasOverflow);
        next.classList.toggle('is-hidden', !hasOverflow);
    }

    previous.addEventListener('click', function () {
        nav.scrollBy({ left: -Math.max(nav.clientWidth * 0.7, 180), behavior: 'smooth' });
    });
    next.addEventListener('click', function () {
        nav.scrollBy({ left: Math.max(nav.clientWidth * 0.7, 180), behavior: 'smooth' });
    });
    nav.addEventListener('scroll', updateCategoryArrows, { passive: true });
    window.addEventListener('resize', updateCategoryArrows);
    updateCategoryArrows();

    if (categoryDropdown && categoryDropdownCheck) {
        let dropdownCloseTimer;
        categoryDropdown.addEventListener('mouseenter', function () {
            clearTimeout(dropdownCloseTimer);
        });
        categoryDropdown.addEventListener('mouseleave', function () {
            dropdownCloseTimer = setTimeout(function () {
                categoryDropdownCheck.checked = false;
            }, 500);
        });
    }
});
</script>

<main class="container" style="min-height: calc(100vh - 200px); padding-top: 2rem; padding-bottom: 2rem;">
