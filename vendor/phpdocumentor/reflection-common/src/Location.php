<?php
/**
 * phpDocumentor，Reflection，地址
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
 * The location where an element occurs within a file.
 * 元素在文件中出现的位置
 *
 * @psalm-immutable
 */
final class Location
{
    /** @var int */
    private $lineNumber = 0;

    /** @var int */
    private $columnNumber = 0;

    /**
     * Initializes the location for an element using its line number in the file and optionally the column number.
	 * 初始化元素的位置,使用其行号在文件中,并可选列号。
     */
    public function __construct(int $lineNumber, int $columnNumber = 0)
    {
        $this->lineNumber = $lineNumber;
        $this->columnNumber = $columnNumber;
    }

    /**
     * Returns the line number that is covered by this location.
     */
    public function getLineNumber() : int
    {
        return $this->lineNumber;
    }

    /**
     * Returns the column number (character position on a line) for this location object.
     */
    public function getColumnNumber() : int
    {
        return $this->columnNumber;
    }
}
