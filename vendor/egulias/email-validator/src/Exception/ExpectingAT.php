<?php
/**
 * Egulias，EmailValidator，异常，Expecting AT
 */

namespace Egulias\EmailValidator\Exception;

class ExpectingAT extends InvalidEmail
{
    const CODE = 202;
    const REASON = "Expecting AT '@' ";
}
