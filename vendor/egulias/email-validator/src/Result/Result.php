<?php
/**
 * Egulias，EmailValidator，结果，Result
 */

namespace Egulias\EmailValidator\Result;

interface Result
{
    /**
     * Is validation result valid?
	 * 原因
     */
    public function isValid() : bool;

    /**
     * Is validation result invalid?
     * Usually the inverse of isValid()
     */
    public function isInvalid() : bool;

    /**
     * Short description of the result, human readable.
	 * 对结果的简短描述,人类的可读性。
     */
    public function description() : string;

    /**
     * Code for user land to act upon.
     */
    public function code() : int;
}
