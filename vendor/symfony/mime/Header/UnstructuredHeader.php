<?php
/**
 * Symfony，组件，Mime，数据头，非结构化的头
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Header;

/**
 * A Simple MIME Header.
 * 一个简单的MIME头。
 *
 * @author Chris Corbyn
 */
class UnstructuredHeader extends AbstractHeader
{
    private $value;

    public function __construct(string $name, string $value)
    {
        parent::__construct($name);

        $this->setValue($value);
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->setValue($body);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->getValue();
    }

    /**
     * Get the (unencoded) value of this header.
	 * 获取此标头的（未编码的）值
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the (unencoded) value of this header.
	 * 设置报头的（未编码的）值
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * Get the value of this header prepared for rendering.
	 * 获取准备渲染的头的值
     */
    public function getBodyAsString(): string
    {
        return $this->encodeWords($this, $this->value);
    }
}
