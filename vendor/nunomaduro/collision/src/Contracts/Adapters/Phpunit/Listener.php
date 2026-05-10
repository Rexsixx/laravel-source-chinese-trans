<?php
/**
 * NunoMaduro，Collision，契约，适配器，Php单元，倾听者
 */

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Contracts\Adapters\Phpunit;

use PHPUnit\Framework\TestListener;

/**
 * This is an Collision Phpunit Adapter contract.
 * 这是一个碰撞Phpunit适配器合同
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
interface Listener extends TestListener
{
    /**
     * Renders the provided error
     * on the console.
	 * 呈现控制台上提供的错误
     *
     * @param  \Throwable $t
     *
     * @return void
     */
    public function render(\Throwable $t);
}
