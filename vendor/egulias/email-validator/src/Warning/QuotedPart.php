<?php
/**
 * Egulias，电子邮件验证器，警告，引用的部分
 */

namespace Egulias\EmailValidator\Warning;

class QuotedPart extends Warning
{
    const CODE = 36;

    /**
     * @param scalar $prevToken
     * @param scalar $postToken
     */
    public function __construct($prevToken, $postToken)
    {
        $this->message = "Deprecated Quoted String found between $prevToken and $postToken";
    }
}
