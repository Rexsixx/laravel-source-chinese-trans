<?php
/**
 * Egulias，电子邮件验证器，警告，IPV6双冒号
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
