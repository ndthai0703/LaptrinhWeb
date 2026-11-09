<?php
namespace App\Controllers;

use App\Models\ArticleModel;

class HomeController {
    public function index() {
        $articleModel = new ArticleModel();
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $pageSize = 10;
        $offset = ($currentPage - 1) * $pageSize;
        $articles = $articleModel->getLatestPublished($pageSize, $offset);
        $totalArticles = $articleModel->countPublishedArticles();
        $totalPages = max(1, (int)ceil($totalArticles / $pageSize));
        if ($currentPage > $totalPages) {
            header('Location: index.php?page=' . $totalPages);
            exit;
        }
        
        $featured = $currentPage === 1 && !empty($articles) ? $articles[0] : null;
        $regularArticles = $currentPage === 1 ? array_slice($articles, 1) : $articles;
        $uploadPath = 'assets/images/articles/';

        // Gọi View để hiển thị giao diện
        require_once __DIR__ . '/../Views/public/home.php';
    }
}
