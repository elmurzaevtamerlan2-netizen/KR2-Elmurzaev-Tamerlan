<?php
declare(strict_types=1);
require_once __DIR__ . '/../Model/Product.php';
require_once __DIR__ . '/../Model/Category.php';

final class CatalogService
{
    private array $products = [];
    private array $categories = [];
    public function __construct(string $productsFile, string $categoriesFile)
    {
        $categories = $this->readJson($categoriesFile);
        foreach ($categories as $row) {
            $category = new Category((int)$row['id'], (string)$row['name']);
            $this->categories[$category->id()] = $category;
        }
        foreach ($this->readJson($productsFile) as $row) {
            $product = new Product((int)$row['id'], (string)$row['name'], (float)$row['price'], (int)$row['category_id'], (string)($row['description'] ?? ''));
            if (!isset($this->categories[$product->categoryId()])) throw new UnexpectedValueException('Категория товара не найдена.');
            $this->products[] = $product;
        }
    }
    private function readJson(string $file): array
    {
        $raw = file_get_contents($file);
        if ($raw === false) throw new RuntimeException('Не удалось прочитать ' . basename($file));
        $data = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
        if (!is_array($data)) throw new UnexpectedValueException('Неверный формат JSON.');
        return $data;
    }
    public function all(): array { return $this->products; }
    public function categories(): array { return array_values($this->categories); }
    public function byCategory(int $id): array { return array_values(array_filter($this->products, static fn(Product $p) => $p->categoryId() === $id)); }
    public function averagePrice(): float { return $this->products === [] ? 0 : array_sum(array_map(static fn(Product $p) => $p->price(), $this->products)) / count($this->products); }
}
