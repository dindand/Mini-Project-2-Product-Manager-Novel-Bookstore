<?php
// public/delete.php - mini_projectdnd
session_start();
require_once __DIR__ . "/../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method Not Allowed");
}

$sessionCsrf = $_SESSION["csrf"] ?? "";
$postCsrf    = $_POST["csrf"] ?? "";

if (!hash_equals($sessionCsrf, $postCsrf)) {
    http_response_code(403);
    exit("Akses Ditolak: Token CSRF tidak valid.");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php?status=notfound");
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(["id" => $id]);

    header("Location: index.php?status=deleted");
    exit;
} catch (PDOException $e) {
    die("Gagal menghapus data novel: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8"));
}
