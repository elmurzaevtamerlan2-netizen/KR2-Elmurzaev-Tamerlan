<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/functions.php';

$dataFile = __DIR__ . '/../dataset/employees.csv';
$employees = array_map('employeeFromRow', loadEmployees($dataFile));
$form = ['name'=>'', 'email'=>'', 'department'=>'', 'salary'=>''];
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $key => $_) $form[$key] = trim((string)($_POST[$key] ?? ''));
    $errors = validateEmployee($form);
    if ($errors === []) {
        $employees[] = ['id'=>count($employees)+1, 'name'=>$form['name'], 'email'=>$form['email'], 'department'=>$form['department'], 'salary'=>(float)$form['salary']];
        $success = true;
        $form = ['name'=>'', 'email'=>'', 'department'=>'', 'salary'=>''];
    }
}
$filter = trim((string)($_GET['department'] ?? ''));
$visible = $filter === '' ? $employees : array_values(array_filter($employees, static fn($item) => $item['department'] === $filter));
$departments = array_unique(array_column($employees, 'department'));
?><!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Практическая 4</title></head><body><main><h1>Практическая работа №4</h1><p>Эльмурзаев Тамерлан Заурович · ЭПБО-02-25</p><p>Версия 1.0 · <?= e(date('d.m.Y')) ?></p>
<?php if (!is_file($dataFile)): ?><p>Не найден файл dataset/employees.csv. Добавьте исходный файл из материалов преподавателя.</p><?php endif; ?>
<?php if ($success): ?><p>Запись добавлена в текущий список.</p><?php endif; ?>
<?php if ($errors): ?><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul><?php endif; ?>
<form method="post"><label>ФИО <input name="name" value="<?= e($form['name']) ?>"></label><label>Email <input name="email" value="<?= e($form['email']) ?>"></label><label>Отдел <input name="department" value="<?= e($form['department']) ?>"></label><label>Зарплата <input name="salary" type="number" min="0" max="10000000" step="0.01" value="<?= e($form['salary']) ?>"></label><button>Добавить</button></form>
<form method="get"><label>Отдел <select name="department"><option value="">Все</option><?php foreach ($departments as $department): ?><option value="<?= e($department) ?>" <?= $filter===$department?'selected':'' ?>><?= e($department) ?></option><?php endforeach; ?></select></label><button>Фильтр</button></form>
<table><thead><tr><th>ID</th><th>ФИО</th><th>Email</th><th>Отдел</th><th>Зарплата</th></tr></thead><tbody><?php foreach ($visible as $item): ?><tr><td><?= e($item['id']) ?></td><td><?= e($item['name']) ?></td><td><?= e($item['email']) ?></td><td><?= e($item['department']) ?></td><td><?= e($item['salary']) ?></td></tr><?php endforeach; ?></tbody></table></main></body></html>
