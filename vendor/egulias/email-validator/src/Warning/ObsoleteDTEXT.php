<?php
/**
 * Egulias，电子邮件验证器，警告，过时的DTEXT
 */

namespace Egulias\EmailValidator\Warning;

class ObsoleteDTEXT extends Warning
{
    public const CODE = 71;

    public function __construct()
    {
        $this->rfcNumber = 5322;
        $this->message = 'Obsolete DTEXT in domain literal';
    }
}
