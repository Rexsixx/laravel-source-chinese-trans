<?php declare(strict_types=1);

/**
 * SebastianBergmann，差速器，最长公共子序列计算器
 */

/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Diff;

interface LongestCommonSubsequenceCalculator
{
    /**
     * Calculates the longest common subsequence of two arrays.
	 * 计算两个数组的最长公共子序列
     *
     * @param array $from
     * @param array $to
     *
     * @return array
     */
    public function calculate(array $from, array $to): array;
}
