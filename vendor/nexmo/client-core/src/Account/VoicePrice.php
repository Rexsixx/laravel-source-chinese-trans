<?php
/**
 * Nexmo，账户，Voice Price
 */

namespace Nexmo\Account;

class VoicePrice extends Price {
    protected $priceMethod = 'getOutboundVoicePrice';
}
