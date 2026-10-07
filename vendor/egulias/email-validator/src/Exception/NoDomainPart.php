<?php
/**
 * Egulias，EmailValidator，异常，无域名部分
 */

namespace Egulias\EmailValidator\Exception;

class NoDomainPart extends InvalidEmail
{
    const CODE = 131;
    const REASON = "No Domain part";
}
