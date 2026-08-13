<?php
/**
 * Egulias，EmailValidator，异常，CRLF At The End
 */

namespace Egulias\EmailValidator\Exception;

class CRLFAtTheEnd extends InvalidEmail
{
    const CODE = 149;
    const REASON = "CRLF at the end";
}
