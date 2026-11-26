<?php
declare(strict_types=1);

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function loadEmployees(string $file): array
{
    if (!is_file($file) || !is_readable($file)) {
        return [];
    }
    $handle = fopen($file, 'r');
    if ($handle === false) {
        return [];
    }
    $headers = fgetcsv($handle);
    $employees = [];
    if (is_array($headers)) {
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $employees[] = array_combine($headers, $row);
            }
        }
    }
    fclose($handle);
    return $employees;
}

function validateEmployee(array $data): array
{
    $errors = [];
    if ($data['name'] === '') $errors['name'] = 'Введите ФИО.';
    if (mb_strlen($data['name']) > 120) $errors['name'] = 'ФИО слишком длинное.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Проверьте email.';
    if ($data['department'] === '') $errors['department'] = 'Укажите отдел.';
    if ($data['salary'] === '' || !is_numeric($data['salary']) || (float)$data['salary'] < 0 || (float)$data['salary'] > 10000000) {
        $errors['salary'] = 'Зарплата должна быть числом от 0 до 10000000.';
    }
    return $errors;
}

function employeeFromRow(array $row): array
{
    return ['id'=>(int)($row['id'] ?? 0), 'name'=>(string)($row['name'] ?? ''), 'email'=>(string)($row['email'] ?? ''), 'department'=>(string)($row['department'] ?? ''), 'salary'=>(float)($row['salary'] ?? 0)];
}
