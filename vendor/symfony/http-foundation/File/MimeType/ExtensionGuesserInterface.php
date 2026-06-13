<?php
/**
 * Symfony，组件，HTTP基础，文件，Mime类型，扩展猜测界面
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\File\MimeType;

use Symfony\Component\Mime\MimeTypesInterface;

/**
 * Guesses the file extension corresponding to a given mime type.
 * 猜测对应于给定mime类型的文件扩展。
 *
 * @deprecated since Symfony 4.3, use {@link MimeTypesInterface} instead
 */
interface ExtensionGuesserInterface
{
    /**
     * Makes a best guess for a file extension, given a mime type.
	 * 在给定mime类型的情况下，对文件扩展名进行最佳猜测。
     *
     * @param string $mimeType The mime type
     *
     * @return string The guessed extension or NULL, if none could be guessed
     */
    public function guess($mimeType);
}
