<?php
/**
 * Symfony，组件，探测器，迭代器，大小范围过滤器迭代器
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Iterator;

use Symfony\Component\Finder\Comparator\NumberComparator;

/**
 * SizeRangeFilterIterator filters out files that are not in the given size range.
 * SizeRangeFilterIterator过滤掉不在给定大小范围内的文件。
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SizeRangeFilterIterator extends \FilterIterator
{
    private $comparators = [];

    /**
     * @param \Iterator          $iterator    The Iterator to filter
     * @param NumberComparator[] $comparators An array of NumberComparator instances
     */
    public function __construct(\Iterator $iterator, array $comparators)
    {
        $this->comparators = $comparators;

        parent::__construct($iterator);
    }

    /**
     * Filters the iterator values.
	 * 过滤迭代器值
     *
     * @return bool true if the value should be kept, false otherwise
     */
    #[\ReturnTypeWillChange]
    public function accept()
    {
        $fileinfo = $this->current();
        if (!$fileinfo->isFile()) {
            return true;
        }

        $filesize = $fileinfo->getSize();
        foreach ($this->comparators as $compare) {
            if (!$compare->test($filesize)) {
                return false;
            }
        }

        return true;
    }
}
