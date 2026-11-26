<?php
declare(strict_types=1);

function loadJsonArray(string $file): array
{
    if (!is_file($file) || !is_readable($file)) return [];
    $raw = file_get_contents($file);
    if ($raw === false) return [];
    try { $value = json_decode($raw, true, flags: JSON_THROW_ON_ERROR); }
    catch (JsonException $exception) { return []; }
    return is_array($value) ? $value : [];
}
function saveJsonArray(string $file, array $value): bool
{
    try { $json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR); }
    catch (JsonException $exception) { return false; }
    return file_put_contents($file, $json . PHP_EOL, LOCK_EX) !== false;
}
function render(string $template, array $data = []): void
{
    $path = __DIR__ . '/../templates/' . $template;
    if (!is_file($path)) throw new RuntimeException('Шаблон не найден.');
    extract($data, EXTR_SKIP);
    require $path;
}
