<?php
/**
 * Egulias，EmailValidator，异常，Expecting CTEXT
 */

namespace Egulias\EmailValidator\Exception;

class ExpectingCTEXT extends InvalidEmail
{
    const CODE = 139;
    const REASON = "Expecting CTEXT";
}
