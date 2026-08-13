<?php
/**
 * Egulias，EmailValidator，警告，TLD
 */

namespace Egulias\EmailValidator\Warning;

class TLD extends Warning
{
    const CODE = 9;

    public function __construct()
    {
        $this->message = "RFC5321, TLD";
    }
}
