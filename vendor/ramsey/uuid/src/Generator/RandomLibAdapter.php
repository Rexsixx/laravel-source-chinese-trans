<?php
/**
 * Ramsey，Uuid，生成器，随机类适配器
 */

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Generator;

use RandomLib\Generator;
use RandomLib\Factory;

/**
 * RandomLibAdapter provides functionality to generate strings of random
 * binary data using the paragonie/random-lib library
 * 随机变量提供了使用“共生”/随机- lib库来生成随机二进制数据字符串的功能。
 *
 * @link https://packagist.org/packages/paragonie/random-lib
 */
class RandomLibAdapter implements RandomGeneratorInterface
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * Constructs a `RandomLibAdapter` using a `RandomLib\Generator`
	 * 使用“随机自由\生成器”构造一个“随机变量”。
     *
     * By default, if no `Generator` is passed in, this creates a high-strength
     * generator to use when generating random binary data.
     *
     * @param Generator $generator An paragonie/random-lib `Generator`
     */
    public function __construct(Generator $generator = null)
    {
        $this->generator = $generator;

        if ($this->generator === null) {
            $factory = new Factory();

            $this->generator = $factory->getHighStrengthGenerator();
        }
    }

    /**
     * Generates a string of random binary data of the specified length
	 * 生成指定长度的随机二进制数据串
     *
     * @param integer $length The number of bytes of random binary data to generate
     * @return string A binary string
     */
    public function generate($length)
    {
        return $this->generator->generate($length);
    }
}
