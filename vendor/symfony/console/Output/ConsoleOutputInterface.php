<?php
/**
 * Symfony，组件，控制台，输出，控制台输出接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Output;

/**
 * ConsoleOutputInterface is the interface implemented by ConsoleOutput class.
 * This adds information about stderr and section output stream.
 *
 * @author Dariusz Górecki <darek.krk@gmail.com>
 *
 * @method ConsoleSectionOutput section() Creates a new output section
 */
interface ConsoleOutputInterface extends OutputInterface
{
    /**
     * Gets the OutputInterface for errors.
     *
     * @return OutputInterface
     */
    public function getErrorOutput();

    public function setErrorOutput(OutputInterface $error);
}
