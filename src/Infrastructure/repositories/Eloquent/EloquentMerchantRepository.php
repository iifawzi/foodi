<?php

namespace Src\Infrastructure\repositories\Eloquent;

use Src\Application\ports\infrastructure\repositories\MerchantRepository;
use Src\Domain\Entities\Merchant;

class EloquentMerchantRepository implements MerchantRepository
{
    public function getMerchant(int $merchantId): Merchant
    {
        /**
 * @var \App\Models\Merchant $merchant 
*/
        $merchant = \App\Models\Merchant::query()->findOrFail($merchantId);
        return new Merchant($merchant["merchant_id"], $merchant["name"], $merchant["email"]);
    }
}
