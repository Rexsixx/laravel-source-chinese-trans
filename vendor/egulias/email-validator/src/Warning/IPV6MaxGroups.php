<?php
/**
 * Egulias，电子邮件验证器，警告，IPV6最大组数
 */

namespace Egulias\EmailValidator\Warning;

class IPV6MaxGroups extends Warning
{
    const CODE = 75;

    public function __construct()
    {
        $this->message = 'Reached the maximum number of IPV6 groups allowed';
        $this->rfcNumber = 5321;
    }
}
