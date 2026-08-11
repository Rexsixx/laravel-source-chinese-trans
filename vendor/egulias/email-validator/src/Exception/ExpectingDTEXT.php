<?php
/**
 * Egulias，电子邮件验证器，异常，Expecting DTEXT
 */

namespace Egulias\EmailValidator\Exception;

class ExpectingDTEXT extends InvalidEmail
{
    const CODE = 129;
    const REASON = "Expected DTEXT";
}
