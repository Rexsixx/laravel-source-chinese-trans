<?php
/**
 * NunoMaduro，Collision，契约，参数格式化程序
 */

/*
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Contracts;

/**
 * This is an Collision Argument Formatter contract.
 * 这是一个碰撞论证格式契约
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
interface ArgumentFormatter
{
    /**
     * Formats the provided array of arguments into
     * an understandable description.
	 * 将提供的参数数组格式格式化为一个可以理解的描述
     *
     * @param  array $arguments
     * @param  bool $recursive
     *
     * @return string
     */
    public function format(array $arguments, bool $recursive = true): string;
}
