<?php
/**
 * League，Mime类型检测，扩展到 Mime类型映射
 */

declare(strict_types=1);

namespace League\MimeTypeDetection;

interface ExtensionToMimeTypeMap
{
    public function lookupMimeType(string $extension): ?string;
}
