<?php
/**
 * Symfony，组件，翻译，阅读器，翻译阅读器接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Reader;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * TranslationReader reads translation messages from translation files.
 * TranslationReader从翻译文件中读取翻译消息。
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface TranslationReaderInterface
{
    /**
     * Reads translation messages from a directory to the catalogue.
	 * 将翻译消息从目录读取到目录
     *
     * @param string $directory
     */
    public function read($directory, MessageCatalogue $catalogue);
}
