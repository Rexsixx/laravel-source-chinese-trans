<?php
/**
 * Nexmo，账户，前缀价格
 */

namespace Nexmo\Account;

class PrefixPrice extends Price {
    protected $priceMethod = 'getPrefixPrice';

    public function getCurrency()
    {
        throw new Exception('Currency is unavailable from this endpoint');
    }
}

