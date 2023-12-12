<?php

namespace Src\Application\ports\infrastructure;
use Src\Domain\Entities\Merchant;

interface MerchantRepository
{
    public function getMerchant(int $merchantId): Merchant;
}
