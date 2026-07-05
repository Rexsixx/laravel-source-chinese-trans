<?php
/**
 * Egulias，EmailValidator，结果，原因，详细原因
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
