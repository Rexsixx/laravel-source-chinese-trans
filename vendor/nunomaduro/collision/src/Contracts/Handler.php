<?php
/**
 * NunoMaduro，Collision，契约，处理者
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

use Whoops\Handler\HandlerInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This is an Collision Handler contract.
 * 这是一个碰撞处理合同
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
interface Handler extends HandlerInterface
{
    /**
     * Sets the output.
	 * 设置输出
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \NunoMaduro\Collision\Contracts\Handler
     */
    public function setOutput(OutputInterface $output): Handler;

    /**
     * Returns the writer.
	 * 返回作者
     *
     * @return \NunoMaduro\Collision\Contracts\Writer
     */
    public function getWriter(): Writer;
}
