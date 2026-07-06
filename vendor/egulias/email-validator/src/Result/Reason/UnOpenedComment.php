<?php
/**
 * Egulias，电子邮件验证器，结果，理由，未打开的评论
 */

namespace Egulias\EmailValidator\Result\Reason;

class UnOpenedComment implements Reason
{
    public function code() : int
    {
        return 152;
    }

    public function description(): string
    {
        return 'Missing opening comment parentheses - https://tools.ietf.org/html/rfc5322#section-3.2.2';
    }
}
