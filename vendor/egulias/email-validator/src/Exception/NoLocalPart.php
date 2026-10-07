<?php
/**
 * Egulias，EmailValidator，异常，无本地部分
 */

namespace Egulias\EmailValidator\Exception;

class NoLocalPart extends InvalidEmail
{
    const CODE = 130;
    const REASON = "No local part";
}
