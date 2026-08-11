<?php
/**
 * Egulias，电子邮件验证器，异常，Expecting ATEXT
 */

namespace Egulias\EmailValidator\Exception;

class ExpectingATEXT extends InvalidEmail
{
    const CODE = 137;
    const REASON = "Expecting ATEXT";
}
