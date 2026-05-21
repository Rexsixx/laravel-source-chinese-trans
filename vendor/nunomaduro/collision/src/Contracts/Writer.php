<?php
/**
 * NunoMaduro，Collision，契约，作者
 */

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Contracts;

use Whoops\Exception\Inspector;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This is the Collision Writer contract.
 * 这是碰撞作家合同
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
interface Writer
{
    /**
     * Ignores traces where the file string matches one
     * of the provided regex expressions.
	 * 忽略文件字符串匹配提供的regex表达式的跟踪
     *
     * @param  string[] $ignore The regex expressions.
     *
     * @return \NunoMaduro\Collision\Contracts\Writer
     */
    public function ignoreFilesIn(array $ignore): Writer;

    /**
     * Declares whether or not the Writer should show the trace.
	 * 声明作者是否应该显示跟踪
     *
     * @param  bool $show
     *
     * @return \NunoMaduro\Collision\Contracts\Writer
     */
    public function showTrace(bool $show): Writer;

    /**
     * Declares whether or not the Writer should show the editor.
	 * 声明作者是否应该向编辑显示
     *
     * @param  bool $show
     *
     * @return \NunoMaduro\Collision\Contracts\Writer
     */
    public function showEditor(bool $show): Writer;

    /**
     * Writes the details of the exception on the console.
	 * 在控制台写入异常的详细信息
     *
     * @param \Whoops\Exception\Inspector $inspector
     */
    public function write(Inspector $inspector): void;

    /**
     * Sets the output.
	 * 设置输出
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \NunoMaduro\Collision\Contracts\Writer
     */
    public function setOutput(OutputInterface $output): Writer;

    /**
     * Gets the output.
	 * 得到输出
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput(): OutputInterface;
}
