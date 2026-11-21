<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/TaskController.php';

$db = getDBConnection();
$controller = new TaskController($db);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Route matching
if ($uri === '/tasks' && $method === 'POST') {
    $controller->create($input);
} elseif ($uri === '/tasks' && $method === 'GET') {
    $controller->index($_GET['status'] ?? null);
} elseif (preg_match('#^/tasks/(\d+)$#', $uri, $matches)) {
    $id = (int)$matches[1];
    if ($method === 'PATCH') {
        $controller->updateStatus($id, $input);
    } elseif ($method === 'DELETE') {
        $controller->delete($id);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}