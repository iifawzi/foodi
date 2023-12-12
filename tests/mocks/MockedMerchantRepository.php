<?php

namespace Tests\mocks;

use Src\Application\ports\infrastructure\MerchantRepository;
use Src\Domain\Entities\Merchant;

class MockedMerchantRepository implements MerchantRepository
{
    public function getMerchant(int $merchantId): Merchant
    {
        return new Merchant(1, "Fawzi", "iifawzie@gmail.com");
    }
}
