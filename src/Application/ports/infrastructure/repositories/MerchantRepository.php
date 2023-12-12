<?php

namespace Src\Application\ports\infrastructure\repositories;
use Src\Domain\Entities\Merchant;

interface MerchantRepository
{
    public function getMerchant(int $merchantId): Merchant;
}
