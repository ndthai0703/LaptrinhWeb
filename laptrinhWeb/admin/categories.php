<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../app/Helpers/functions.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Models/CategoryModel.php';
require_once __DIR__ . '/../app/Controllers/Admin/CategoryController.php';

use App\Controllers\Admin\CategoryController;

$controller = new CategoryController();
$controller->index();
