<?php
/**
 * Symfony，组件，Mime，Mime类型接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface MimeTypesInterface extends MimeTypeGuesserInterface
{
    /**
     * Gets the extensions for the given MIME type in decreasing order of preference.
	 * 获取给定MIME类型的扩展,以减少优先级的顺序。
     *
     * @return string[]
     */
    public function getExtensions(string $mimeType): array;

    /**
     * Gets the MIME types for the given extension in decreasing order of preference.
	 * 获取给定扩展的MIME类型以减少优先级的顺序
     *
     * @return string[]
     */
    public function getMimeTypes(string $ext): array;
}
