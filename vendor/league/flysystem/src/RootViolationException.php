<?php
/**
 * League，Flysystem，根验证异常
 */

namespace League\Flysystem;

use LogicException;

class RootViolationException extends LogicException implements FilesystemException
{
    //
}
