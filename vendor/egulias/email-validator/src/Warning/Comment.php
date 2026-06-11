<?php
/**
 * Egulias，电子邮件验证器，警告，评论
 */

namespace Egulias\EmailValidator\Warning;

class Comment extends Warning
{
    public const CODE = 17;

    public function __construct()
    {
        $this->message = "Comments found in this email";
    }
}
