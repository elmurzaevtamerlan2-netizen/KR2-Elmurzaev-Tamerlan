<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../src/storage.php';
$notesFile = __DIR__ . '/../dataset/notes.json';
$users = loadJsonArray(__DIR__ . '/../dataset/users.json');
$defaultUser = (string)($users[0]['name'] ?? 'Гость');
$_SESSION['user'] ??= $defaultUser;
$error = '';
$flash = (string)($_SESSION['flash'] ?? '');
unset($_SESSION['flash']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'add') {
        $title = trim((string)($_POST['title'] ?? ''));
        $body = trim((string)($_POST['body'] ?? ''));
        if ($title === '' || $body === '') $error = 'Заполните заголовок и текст.';
        elseif (mb_strlen($title) > 100 || mb_strlen($body) > 2000) $error = 'Превышена допустимая длина текста.';
        else {
            $notes = loadJsonArray($notesFile);
            $notes[] = ['id'=>bin2hex(random_bytes(6)), 'title'=>$title, 'body'=>$body, 'created_at'=>date('d.m.Y H:i')];
            if (saveJsonArray($notesFile, $notes)) { $_SESSION['flash'] = 'Заметка сохранена.'; header('Location: index.php'); exit; }
            $error = 'Не удалось сохранить файл заметок.';
        }
    } elseif ($action === 'clear') { unset($_SESSION['user']); header('Location: index.php'); exit; }
}
if (isset($_GET['user'])) {
    $requested = trim((string)$_GET['user']);
    if (in_array($requested, array_column($users, 'name'), true)) { $_SESSION['user'] = $requested; header('Location: index.php'); exit; }
}
$page = (string)($_GET['page'] ?? 'list');
if ($page !== 'list') { http_response_code(404); $error = 'Страница не найдена (404).'; }
render('list.php', ['notes'=>loadJsonArray($notesFile), 'users'=>$users, 'user'=>(string)($_SESSION['user'] ?? $defaultUser), 'error'=>$error, 'flash'=>$flash]);
