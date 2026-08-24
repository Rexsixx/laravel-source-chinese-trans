<?php
/**
 * Egulias，EmailValidator，异常，本地或保留域
 */

namespace Egulias\EmailValidator\Exception;

class LocalOrReservedDomain extends InvalidEmail
{
    const CODE = 153;
    const REASON = 'Local, mDNS or reserved domain (RFC2606, RFC6762)';
}