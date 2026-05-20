<?php
/**
 * Mockery，匹配程序，包含
 */

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\Matcher;

class Contains extends MatcherAbstract
{
    /**
     * Check if the actual value matches the expected.
	 * 检查实际值是否与预期值匹配
     *
     * @param mixed $actual
     * @return bool
     */
    public function match(&$actual)
    {
        $values = array_values($actual);
        foreach ($this->_expected as $exp) {
            $match = false;
            foreach ($values as $val) {
                if ($exp === $val || $exp == $val) {
                    $match = true;
                    break;
                }
            }
            if ($match === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return a string representation of this Matcher
	 * 返回此匹配器的字符串表示形式
     *
     * @return string
     */
    public function __toString()
    {
        $return = '<Contains[';
        $elements = array();
        foreach ($this->_expected as $v) {
            $elements[] = (string) $v;
        }
        $return .= implode(', ', $elements) . ']>';
        return $return;
    }
}
