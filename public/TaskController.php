<?php

class TaskController {
    private PDO $db;
    private array $allowedStatuses = ['pending', 'in_progress', 'completed'];

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function create(array $data): void {
        if (empty(trim($data['title'] ?? ''))) {
            $this->jsonResponse(['error' => 'Title is required'], 400);
            return;
        }

        $stmt = $this->db->prepare("INSERT INTO tasks (title, description, status) VALUES (:title, :desc, 'pending')");
        $stmt->execute([
            ':title' => trim($data['title']),
            ':desc'  => $data['description'] ?? null
        ]);

        $id = $this->db->lastInsertId();
        $this->jsonResponse(['message' => 'Task created', 'id' => (int)$id], 201);
    }

    public function index(?string $statusFilter): void {
        if ($statusFilter) {
            if (!in_array($statusFilter, $this->allowedStatuses)) {
                $this->jsonResponse(['error' => 'Invalid status filter'], 400);
                return;
            }
            $stmt = $this->db->prepare("SELECT * FROM tasks WHERE status = :status ORDER BY id DESC");
            $stmt->execute([':status' => $statusFilter]);
        } else {
            $stmt = $this->db->query("SELECT * FROM tasks ORDER BY id DESC");
        }

        $this->jsonResponse(['data' => $stmt->fetchAll()], 200);
    }

    public function updateStatus(int $id, array $data): void {
        $status = $data['status'] ?? '';
        if (!in_array($status, $this->allowedStatuses)) {
            $this->jsonResponse(['error' => 'Invalid status. Allowed: pending, in_progress, completed'], 400);
            return;
        }

        $stmt = $this->db->prepare("UPDATE tasks SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);

        if ($stmt->rowCount() === 0) {
            $this->jsonResponse(['error' => 'Task not found'], 404);
            return;
        }

        $this->jsonResponse(['message' => 'Task status updated'], 200);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) {
            $this->jsonResponse(['error' => 'Task not found'], 404);
            return;
        }

        $this->jsonResponse(['message' => 'Task deleted'], 200);
    }

    private function jsonResponse(array $payload, int $statusCode): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }
}