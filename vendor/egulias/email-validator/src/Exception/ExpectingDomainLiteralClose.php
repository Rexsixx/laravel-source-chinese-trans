<?php
/**
 * Egulias，EmailValidator，异常，ExpectingDomainLiteralClose
 */

namespace Egulias\EmailValidator\Exception;

class ExpectingDomainLiteralClose extends InvalidEmail
{
    const CODE = 137;
    const REASON = "Closing bracket ']' for domain literal not found";
}
