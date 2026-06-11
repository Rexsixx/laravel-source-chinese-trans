<?php
/**
 * Nexmo，账户，Sms Price
 */

namespace Nexmo\Account;

class SmsPrice extends Price {
    protected $priceMethod = 'getOutboundSmsPrice';
}
