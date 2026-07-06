<?php
/**
 * Symfony，组件，Mime，数据头，非正式组成的标题
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
	 * 获取这个头的(未编码的)值
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the (unencoded) value of this header.
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * Get the value of this header prepared for rendering.
     */
    public function getBodyAsString(): string
    {
        return $this->encodeWords($this, $this->value);
    }
}
