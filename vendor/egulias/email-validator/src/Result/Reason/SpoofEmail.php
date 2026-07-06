<?php
/**
 * Egulias，电子邮件验证器，结果，理由，传真电子邮件
 */

namespace Egulias\EmailValidator\Result\Reason;

class SpoofEmail implements Reason
{
    public function code() : int
    {
        return 298;
    }

    public function description() : string
    {
        return 'The email contains mixed UTF8 chars that makes it suspicious'; 
    }

}
