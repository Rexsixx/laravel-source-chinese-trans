<?php
/**
 * Egulias，电子邮件验证器，确认，DNS 记录
 */

namespace Egulias\EmailValidator\Validation;

class DNSRecords
{
    
    /**
     * @var array $records
     */
    private $records = [];

    /**
     * @var bool $error
     */
    private $error = false;

    public function __construct(array $records, bool $error = false)
    {
        $this->records = $records;
        $this->error = $error;
    }

    public function getRecords() : array
    {
        return $this->records;
    }

    public function withError() : bool
    {
        return $this->error;
    }


}
