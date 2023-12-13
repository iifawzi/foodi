<?php

namespace Src\Domain\Entities;
use Faker\Core\Uuid;
use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;
use Src\Infrastructure\types\LowStockNotificationType;

class Merchant
{

    /**
     * @var array{
     *     notification_id: UuidInterface,
     *     status: LowStockNotificationType,
     *     ingredient_id: int
     * }[]
     */
    private array $notifications = [];

    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $email
    ) {

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function notifyAboutStock(StockItem $stock): void
    {
        $this->notifications[] = [
            "notification_id" => Str::uuid(),
            "status" => LowStockNotificationType::PENDING,
            "ingredient_id" => $stock->getId(),
        ];
    }

    /**
     * @return array{
     *     notification_id: UuidInterface,
     *     status: LowStockNotificationType,
     *     ingredient_id: int
     * }[]
     */
    public function getNotifications(): array
    {
        return $this->notifications;
    }
}
