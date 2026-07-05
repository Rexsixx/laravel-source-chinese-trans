<?php
/**
 * PhpParser，匹配程序，Doctrine，Doctrine 代理匹配程序
 */

namespace DeepCopy\Matcher\Doctrine;

use DeepCopy\Matcher\Matcher;
use Doctrine\Persistence\Proxy;

/**
 * @final
 */
class DoctrineProxyMatcher implements Matcher
{
    /**
     * Matches a Doctrine Proxy class.
	 * 匹配一个Doctrine代理类
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return $object instanceof Proxy;
    }
}
