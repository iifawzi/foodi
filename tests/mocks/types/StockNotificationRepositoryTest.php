<?php

namespace Tests\mocks\types;

use Src\Application\ports\infrastructure\repositories\StockNotificationRepository;
use Src\Domain\Entities\StockItem;

interface StockNotificationRepositoryTest extends StockNotificationRepository
{
    public function getNotifications(): array;

}
