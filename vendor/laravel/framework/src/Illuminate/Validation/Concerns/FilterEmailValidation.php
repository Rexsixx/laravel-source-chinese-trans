<?php
/**
 * Illuminate，验证，问题，过滤邮件验证
 */

namespace Illuminate\Validation\Concerns;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Warning\Warning;
use Egulias\EmailValidator\Exception\InvalidEmail;
use Egulias\EmailValidator\Validation\EmailValidation;

class FilterEmailValidation implements EmailValidation
{
    /**
     * Returns true if the given email is valid.
	 * 如果给定的电子邮件有效，则返回true。
     *
     * @param  string  $email
     * @param  EmailLexer
     * @return bool
     */
    public function isValid($email, EmailLexer $emailLexer)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Returns the validation error.
	 * 返回验证错误
     *
     * @return InvalidEmail|null
     */
    public function getError()
    {
        //
    }

    /**
     * Returns the validation warnings.
	 * 返回验证警告
     *
     * @return Warning[]
     */
    public function getWarnings()
    {
        return [];
    }
}
