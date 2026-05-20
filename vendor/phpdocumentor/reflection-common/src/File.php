<?php
/**
 * phpDocumentor，Reflection，文件
 */

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

/**
 * Interface for files processed by the ProjectFactory
 * 由ProjectFactory处理的文件的接口
 */
interface File
{
    /**
     * Returns the content of the file as a string.
	 * 以字符串形式返回文件的内容
     */
    public function getContents() : string;

    /**
     * Returns md5 hash of the file.
	 * 返回文件的md5哈希
     */
    public function md5() : string;

    /**
     * Returns an relative path to the file.
     */
    public function path() : string;
}
