<?php
/**
 * Egulias，电子邮件验证器，结果，理由，异常找到
 */

namespace Egulias\EmailValidator\Result\Reason;

class ExceptionFound implements Reason
{
    /**
     * @var \Exception
     */
    private $exception;

    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
        
    }
    public function code() : int
    {
        return 999;
    }

    public function description() : string
    {
        return $this->exception->getMessage();
    }
}
