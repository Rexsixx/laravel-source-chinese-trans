<?php
/**
 * Egulias，EmailValidator，异常，不允许的字符串
 */

namespace Egulias\EmailValidator\Exception;

class UnclosedQuotedString extends InvalidEmail
{
    const CODE = 145;
    const REASON = "Unclosed quoted string";
}
