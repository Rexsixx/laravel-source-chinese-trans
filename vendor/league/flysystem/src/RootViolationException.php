<?php
/**
 * League，Flysystem，根违反异常
 */

namespace League\Flysystem;

use LogicException;

class RootViolationException extends LogicException implements FilesystemException
{
    //
}
