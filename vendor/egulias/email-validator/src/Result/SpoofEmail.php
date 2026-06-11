<?php
/**
 * Egulias，电子邮件验证器，结果，传真电子邮件
 */

namespace Egulias\EmailValidator\Result;

use Egulias\EmailValidator\Result\Reason\SpoofEmail as ReasonSpoofEmail;

class SpoofEmail extends InvalidEmail
{
    public function __construct()
    {
        $this->reason = new ReasonSpoofEmail();
        parent::__construct($this->reason, '');
    }
}
