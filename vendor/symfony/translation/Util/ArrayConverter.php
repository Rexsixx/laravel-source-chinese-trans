<?php
/**
 * Symfony，组件，翻译，工具，数组转换器
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Util;

/**
 * ArrayConverter generates tree like structure from a message catalogue.
 * e.g. this
 *   'foo.bar1' => 'test1',
 *   'foo.bar2' => 'test2'
 * converts to follows:
 *   foo:
 *     bar1: test1
 *     bar2: test2.
 * ArrayConverter从消息目录生成树状结构。
 *
 * @author Gennady Telegin <gtelegin@gmail.com>
 */
class ArrayConverter
{
    /**
     * Converts linear messages array to tree-like array.
     * For example this array('foo.bar' => 'value') will be converted to ['foo' => ['bar' => 'value']].
	 * 将线性消息数组转换为树状数组。
     *
     * @param array $messages Linear messages array
     *
     * @return array Tree-like messages array
     */
    public static function expandToTree(array $messages)
    {
        $tree = [];

        foreach ($messages as $id => $value) {
            $referenceToElement = &self::getElementByPath($tree, explode('.', $id));

            $referenceToElement = $value;

            unset($referenceToElement);
        }

        return $tree;
    }

    private static function &getElementByPath(array &$tree, array $parts)
    {
        $elem = &$tree;
        $parentOfElem = null;

        foreach ($parts as $i => $part) {
            if (isset($elem[$part]) && \is_string($elem[$part])) {
                /* Process next case:
                 *    'foo': 'test1',
                 *    'foo.bar': 'test2'
                 *
                 * $tree['foo'] was string before we found array {bar: test2}.
                 *  Treat new element as string too, e.g. add $tree['foo.bar'] = 'test2';
                 */
                $elem = &$elem[implode('.', \array_slice($parts, $i))];
                break;
            }
            $parentOfElem = &$elem;
            $elem = &$elem[$part];
        }

        if ($elem && \is_array($elem) && $parentOfElem) {
            /* Process next case:
             *    'foo.bar': 'test1'
             *    'foo': 'test2'
             *
             * $tree['foo'] was array = {bar: 'test1'} before we found string constant `foo`.
             * Cancel treating $tree['foo'] as array and cancel back it expansion,
             *  e.g. make it $tree['foo.bar'] = 'test1' again.
             */
            self::cancelExpand($parentOfElem, $part, $elem);
        }

        return $elem;
    }

    private static function cancelExpand(array &$tree, $prefix, array $node)
    {
        $prefix .= '.';

        foreach ($node as $id => $value) {
            if (\is_string($value)) {
                $tree[$prefix.$id] = $value;
            } else {
                self::cancelExpand($tree, $prefix.$id, $value);
            }
        }
    }
}
