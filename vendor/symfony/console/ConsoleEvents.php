<?php
/**
 * Symfony，组件，控制台，控制台事件
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console;

/**
 * Contains all events dispatched by an Application.
 * 包含应用程序分派的所有事件。
 *
 * @author Francesco Levorato <git@flevour.net>
 */
final class ConsoleEvents
{
    /**
     * The COMMAND event allows you to attach listeners before any command is
     * executed by the console. It also allows you to modify the command, input and output
     * before they are handed to the command.
	 * 命令事件允许您在控制台执行任何命令之前附加侦听器。
	 * 它还允许您在命令、输入和输出之前修改命令、输入和输出。
     *
     * @Event("Symfony\Component\Console\Event\ConsoleCommandEvent")
     */
    public const COMMAND = 'console.command';

    /**
     * The TERMINATE event allows you to attach listeners after a command is
     * executed by the console.
	 * 终止事件允许您在控制台执行命令后附加侦听器。
     *
     * @Event("Symfony\Component\Console\Event\ConsoleTerminateEvent")
     */
    public const TERMINATE = 'console.terminate';

    /**
     * The ERROR event occurs when an uncaught exception or error appears.
     *
     * This event allows you to deal with the exception/error or
     * to modify the thrown exception.
     *
     * @Event("Symfony\Component\Console\Event\ConsoleErrorEvent")
     */
    public const ERROR = 'console.error';
}
