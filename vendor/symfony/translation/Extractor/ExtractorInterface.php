<?php
/**
 * Symfony，组件，翻译，提取器，提取器接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Extractor;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * Extracts translation messages from a directory or files to the catalogue.
 * New found messages are injected to the catalogue using the prefix.
 * 从目录或文件中提取翻译消息并导入到词典中。新发现的消息将通过前缀注入到词典中。
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 */
interface ExtractorInterface
{
    /**
     * Extracts translation messages from files, a file or a directory to the catalogue.
	 * 从文件、文件或目录中提取翻译消息到目录。
     *
     * @param string|iterable<string> $resource Files, a file or a directory
     */
    public function extract($resource, MessageCatalogue $catalogue);

    /**
     * Sets the prefix that should be used for new found messages.
	 * 设置应用于新发现消息的前缀
     *
     * @param string $prefix The prefix
     */
    public function setPrefix($prefix);
}
