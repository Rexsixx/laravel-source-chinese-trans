<?php
/**
 * Egulias，EmailValidator，异常，未关闭的评论
 */

namespace Egulias\EmailValidator\Exception;

class UnclosedComment extends InvalidEmail
{
    const CODE = 146;
    const REASON = "No closing comment token found";
}
