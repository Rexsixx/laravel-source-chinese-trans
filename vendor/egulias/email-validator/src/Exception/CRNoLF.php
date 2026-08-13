<?php
/**
 * Egulias，EmailValidator，异常，CRNoLF
 */

namespace Egulias\EmailValidator\Exception;

class CRNoLF extends InvalidEmail
{
    const CODE = 150;
    const REASON = "Missing LF after CR";
}
