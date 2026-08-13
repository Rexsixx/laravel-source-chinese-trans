<?php
/**
 * Egulias，EmailValidator，警告，地址常量
 */

namespace Egulias\EmailValidator\Warning;

class DomainLiteral extends Warning
{
    const CODE = 70;

    public function __construct()
    {
        $this->message = 'Domain Literal';
        $this->rfcNumber = 5322;
    }
}
