<?php
/**
 * Egulias，电子邮件验证器，结果，理由，标签太长
 */

namespace Egulias\EmailValidator\Result\Reason;

class LabelTooLong implements Reason
{
    public function code() : int
    {
        return 245;
    }

    public function description() : string
    {
        return 'Domain "label" is longer than 63 characters';
    }
}
