<?php
/**
 * Egulias，电子邮件验证器，结果，理由，详细原因
 */

namespace Egulias\EmailValidator\Result\Reason;

abstract class DetailedReason implements Reason
{
    protected $detailedDescription;

    public function __construct(string $details)
    {
        $this->detailedDescription = $details;
    }
}
