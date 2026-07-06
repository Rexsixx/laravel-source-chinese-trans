<?php
/**
 * Illuminate，基础，引导程序，加载环境变量
 */

namespace Illuminate\Foundation\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Environment\Adapter\PutenvAdapter;
use Symfony\Component\Console\Input\ArgvInput;
use Dotenv\Environment\Adapter\EnvConstAdapter;
use Illuminate\Contracts\Foundation\Application;
use Dotenv\Environment\Adapter\ServerConstAdapter;
use Symfony\Component\Console\Output\ConsoleOutput;

class LoadEnvironmentVariables
{
    /**
     * Bootstrap the given application.
	 * 引导给定的应用程序
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        if ($app->configurationIsCached()) {
            return;
        }

        $this->checkForSpecificEnvironmentFile($app);

        try {
            $this->createDotenv($app)->safeLoad();
        } catch (InvalidFileException $e) {
            $this->writeErrorAndDie($e);
        }
    }

    /**
     * Detect if a custom environment file matching the APP_ENV exists.
	 * 检测是否存在与APP_ENV匹配的自定义环境文件
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function checkForSpecificEnvironmentFile($app)
    {
        if ($app->runningInConsole() && ($input = new ArgvInput)->hasParameterOption('--env')) {
            if ($this->setEnvironmentFilePath(
                $app, $app->environmentFile().'.'.$input->getParameterOption('--env')
            )) {
                return;
            }
        }

        if (! env('APP_ENV')) {
            return;
        }

        $this->setEnvironmentFilePath(
            $app, $app->environmentFile().'.'.env('APP_ENV')
        );
    }

    /**
     * Load a custom environment file.
	 * 加载自定义环境文件
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  string  $file
     * @return bool
     */
    protected function setEnvironmentFilePath($app, $file)
    {
        if (file_exists($app->environmentPath().'/'.$file)) {
            $app->loadEnvironmentFrom($file);

            return true;
        }

        return false;
    }

    /**
     * Create a Dotenv instance.
	 * 创建一个Dotenv实例
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return \Dotenv\Dotenv
     */
    protected function createDotenv($app)
    {
        return Dotenv::create(
            $app->environmentPath(),
            $app->environmentFile(),
            new DotenvFactory([new EnvConstAdapter, new ServerConstAdapter, new PutenvAdapter])
        );
    }

    /**
     * Write the error information to the screen and exit.
	 * 将错误信息写入屏幕并退出
     *
     * @param  \Dotenv\Exception\InvalidFileException  $e
     * @return void
     */
    protected function writeErrorAndDie(InvalidFileException $e)
    {
        $output = (new ConsoleOutput)->getErrorOutput();

        $output->writeln('The environment file is invalid!');
        $output->writeln($e->getMessage());

        die(1);
    }
}
