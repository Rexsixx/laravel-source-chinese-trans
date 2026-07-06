<?php
/**
 * Egulias，电子邮件验证器，结果，理由，无法获得DNS记录
 */

namespace Egulias\EmailValidator\Result\Reason;

/**
 * Used on SERVFAIL, TIMEOUT or other runtime and network errors
 * 用于伺服器故障、超时或其他运行时和网络错误。
 */
class UnableToGetDNSRecord extends NoDNSRecord
{
    public function code() : int
    {
        return 3;
    }

    public function description() : string
    {
        return 'Unable to get DNS records for the host';
    }
}
