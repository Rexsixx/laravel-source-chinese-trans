<?php
/**
 * League，MimeTypeDetection，Mime类型映射的空扩展
 */

declare(strict_types=1);

namespace League\MimeTypeDetection;

class EmptyExtensionToMimeTypeMap implements ExtensionToMimeTypeMap
{
    public function lookupMimeType(string $extension): ?string
    {
        return null;
    }
}
