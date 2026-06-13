<?php
/**
 * Egulias，电子邮件验证器，结果，理由，期待的 ATEXT
 */

namespace Egulias\EmailValidator\Result\Reason;

class ExpectingATEXT extends DetailedReason
{
    public function code() : int
    {
        return 137;
    }

    public function description() : string
    {
        return "Expecting ATEXT (Printable US-ASCII). Extended: " . $this->detailedDescription;
    }
}
