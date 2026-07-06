<?php
/**
 * Egulias，电子邮件验证器，结果，理由，未关闭的评论
 */

namespace Egulias\EmailValidator\Result\Reason;

class UnclosedComment implements Reason 
{
    public function code() : int
    {
        return 146;
    }

    public function description(): string
    {
        return 'No closing comment token found';
    }
}
