<?php
/**
 * Egulias，EmailValidator，异常，Expecting DTEXT
 */

namespace Egulias\EmailValidator\Exception;

class ExpectingDTEXT extends InvalidEmail
{
    const CODE = 129;
    const REASON = "Expected DTEXT";
}
