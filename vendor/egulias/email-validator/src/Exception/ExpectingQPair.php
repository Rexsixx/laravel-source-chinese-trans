<?php
/**
 * Egulias，电子邮件验证器，异常，Expecting QPair
 */

namespace Egulias\EmailValidator\Exception;

class ExpectingQPair extends InvalidEmail
{
    const CODE = 136;
    const REASON = "Expecting QPAIR";
}
