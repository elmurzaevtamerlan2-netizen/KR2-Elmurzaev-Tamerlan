<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Service/CatalogService.php';
function e(string|int|float|null $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$error = '';
try {
    $catalog = new CatalogService(__DIR__ . '/../dataset/products.json', __DIR__ . '/../dataset/categories.json');
    $categoryId = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
    $categoryId = $categoryId === false || $categoryId === null ? 0 : $categoryId;
    $products = $categoryId > 0 ? $catalog->byCategory($categoryId) : $catalog->all();
} catch (Throwable $exception) { $catalog = null; $products = []; $error = 'Не удалось загрузить каталог. Проверьте файлы dataset.'; }
?><!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Практическая 5</title></head><body><main><h1>Практическая работа №5</h1><p>Эльмурзаев Тамерлан Заурович · ЭПБО-02-25</p>
<?php if ($error !== ''): ?><p><?= e($error) ?></p><?php elseif ($catalog): ?><p>Товаров: <?= e(count($catalog->all())) ?>. Средняя цена: <?= e(number_format($catalog->averagePrice(), 2, ',', ' ')) ?> ₽.</p><form method="get"><label>Категория <select name="category"><option value="0">Все категории</option><?php foreach ($catalog->categories() as $category): ?><option value="<?= e($category->id()) ?>" <?= (int)$categoryId === $category->id() ? 'selected' : '' ?>><?= e($category->name()) ?></option><?php endforeach; ?></select></label><button>Показать</button></form><section><?php foreach ($products as $product): ?><article><h2><?= e($product->name()) ?></h2><p><?= e($product->description() !== '' ? $product->description() : 'Описание не указано') ?></p><p><?= e($product->formattedPrice()) ?></p><p>Цена со скидкой 10%: <?= e(number_format($product->priceWithDiscount(10), 2, ',', ' ')) ?> ₽</p></article><?php endforeach; ?></section><?php endif; ?></main></body></html>
