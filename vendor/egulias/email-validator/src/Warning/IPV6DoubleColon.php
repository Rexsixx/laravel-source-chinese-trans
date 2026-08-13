<?php
/**
 * Egulias，EmailValidator，警告，IPV6 双号
 */

namespace Egulias\EmailValidator\Warning;

class IPV6DoubleColon extends Warning
{
    const CODE = 73;

    public function __construct()
    {
        $this->message = 'Double colon found after IPV6 tag';
        $this->rfcNumber = 5322;
    }
}
