<?php
/**
 * Egulias，电子邮件验证器，结果，Result
 */

namespace Egulias\EmailValidator\Result;

interface Result
{
    /**
     * Is validation result valid?
	 * 验证结果有效吗?
     */
    public function isValid() : bool;

    /**
     * Is validation result invalid?
     * Usually the inverse of isValid()
	 * 验证结果无效吗?
     */
    public function isInvalid() : bool;

    /**
     * Short description of the result, human readable.
	 * 对结果的简短描述,人类的可读性。
     */
    public function description() : string;

    /**
     * Code for user land to act upon.
	 * 用户的代码
     */
    public function code() : int;
}
