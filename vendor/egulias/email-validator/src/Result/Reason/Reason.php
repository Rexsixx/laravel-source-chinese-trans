<?php
/**
 * Egulias，电子邮件验证器，结果，理由，Reason
 */

namespace Egulias\EmailValidator\Result\Reason;

interface Reason
{
    /**
     * Code for user land to act upon;
	 * 用户代码
     */
    public function code() : int;

    /**
     * Short description of the result, human readable.
	 * 对结果的简短描述,人类的可读性。
     */
    public function description() : string;
}
