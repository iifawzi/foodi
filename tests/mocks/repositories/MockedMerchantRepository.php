<?php

namespace Tests\mocks\repositories;

use Src\Application\ports\infrastructure\repositories\MerchantRepository;
use Src\Domain\Entities\Merchant;

class MockedMerchantRepository implements MerchantRepository
{
    public function getMerchant(int $merchantId): Merchant
    {
        return new Merchant(1, "Fawzi", "iifawzie@gmail.com");
    }
}
