<?php
declare(strict_types=1);

final class Product
{
    public function __construct(private int $id, private string $name, private float $price, private int $categoryId, private string $description = '')
    {
        if ($id < 1 || trim($name) === '' || $price < 0 || $categoryId < 1) {
            throw new InvalidArgumentException('Некорректные данные товара.');
        }
    }
    public function id(): int { return $this->id; }
    public function name(): string { return $this->name; }
    public function price(): float { return $this->price; }
    public function categoryId(): int { return $this->categoryId; }
    public function description(): string { return $this->description; }
    public function formattedPrice(): string { return number_format($this->price, 2, ',', ' ') . ' ₽'; }
    public function priceWithDiscount(float $percent): float
    {
        if ($percent < 0 || $percent > 100) throw new InvalidArgumentException('Скидка должна быть от 0 до 100%.');
        return $this->price * (1 - $percent / 100);
    }
    public function isCheaperThan(float $limit): bool { return $this->price <= $limit; }
    public function hasDescription(): bool { return trim($this->description) !== ''; }
}
