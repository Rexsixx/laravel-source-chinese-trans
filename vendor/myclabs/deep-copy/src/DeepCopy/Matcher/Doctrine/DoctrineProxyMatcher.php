<?php
/**
 * 深拷贝，匹配程序，Doctrine，Doctrine代理匹配器
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
