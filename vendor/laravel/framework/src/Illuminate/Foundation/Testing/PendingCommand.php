<?php
/**
 * Illuminate，基础，测试，待定的命令
 */

namespace Illuminate\Foundation\Testing;

use Mockery;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Console\Input\ArrayInput;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Mockery\Exception\NoMatchingExpectationException;

class PendingCommand
{
    /**
     * The test being run.
	 * 正在运行的测试
     *
     * @var \Illuminate\Foundation\Testing\TestCase
     */
    public $test;

    /**
     * The application instance.
	 * 程序实例
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The command to run.
	 * 要运行的命令
     *
     * @var string
     */
    protected $command;

    /**
     * The parameters to pass to the command.
	 * 要传递给命令的参数
     *
     * @var array
     */
    protected $parameters;

    /**
     * The expected exit code.
	 * 预期的退出代码
     *
     * @var int
     */
    protected $expectedExitCode;

    /**
     * Determine if command has executed.
	 * 判断命令是否已执行
     *
     * @var bool
     */
    protected $hasExecuted = false;

    /**
     * Create a new pending console command run.
	 * 创建一个新的暂挂控制台命令运行
     *
     * @param  \PHPUnit\Framework\TestCase  $test
     * @param  \Illuminate\Foundation\Application  $app
     * @param  string  $command
     * @param  array  $parameters
     * @return void
     */
    public function __construct(PHPUnitTestCase $test, $app, $command, $parameters)
    {
        $this->app = $app;
        $this->test = $test;
        $this->command = $command;
        $this->parameters = $parameters;
    }

    /**
     * Specify a question that should be asked when the command runs.
	 * 指定命令运行时应该询问的问题
     *
     * @param  string  $question
     * @param  string  $answer
     * @return $this
     */
    public function expectsQuestion($question, $answer)
    {
        $this->test->expectedQuestions[] = [$question, $answer];

        return $this;
    }

    /**
     * Specify output that should be printed when the command runs.
	 * 指定命令运行时应该打印的输出
     *
     * @param  string  $output
     * @return $this
     */
    public function expectsOutput($output)
    {
        $this->test->expectedOutput[] = $output;

        return $this;
    }

    /**
     * Assert that the command has the given exit code.
	 * 断言该命令具有给定的退出代码
     *
     * @param  int  $exitCode
     * @return $this
     */
    public function assertExitCode($exitCode)
    {
        $this->expectedExitCode = $exitCode;

        return $this;
    }

    /**
     * Execute the command.
	 * 执行命令
     *
     * @return int
     */
    public function execute()
    {
        return $this->run();
    }

    /**
     * Execute the command.
	 * 执行命令
     *
     * @return int
     */
    public function run()
    {
        $this->hasExecuted = true;

        $this->mockConsoleOutput();

        try {
            $exitCode = $this->app[Kernel::class]->call($this->command, $this->parameters);
        } catch (NoMatchingExpectationException $e) {
            if ($e->getMethodName() === 'askQuestion') {
                $this->test->fail('Unexpected question "'.$e->getActualArguments()[0]->getQuestion().'" was asked.');
            }

            throw $e;
        }

        if ($this->expectedExitCode !== null) {
            $this->test->assertEquals(
                $this->expectedExitCode, $exitCode,
                "Expected status code {$this->expectedExitCode} but received {$exitCode}."
            );
        }

        return $exitCode;
    }

    /**
     * Mock the application's console output.
	 * 模拟应用程序的控制台输出
     *
     * @return void
     */
    protected function mockConsoleOutput()
    {
        $mock = Mockery::mock(OutputStyle::class.'[askQuestion]', [
            (new ArrayInput($this->parameters)), $this->createABufferedOutputMock(),
        ]);

        foreach ($this->test->expectedQuestions as $i => $question) {
            $mock->shouldReceive('askQuestion')
                ->once()
                ->ordered()
                ->with(Mockery::on(function ($argument) use ($question) {
                    return $argument->getQuestion() == $question[0];
                }))
                ->andReturnUsing(function () use ($question, $i) {
                    unset($this->test->expectedQuestions[$i]);

                    return $question[1];
                });
        }

        $this->app->bind(OutputStyle::class, function () use ($mock) {
            return $mock;
        });
    }

    /**
     * Create a mock for the buffered output.
	 * 为缓冲的输出创建一个模拟
     *
     * @return \Mockery\MockInterface
     */
    private function createABufferedOutputMock()
    {
        $mock = Mockery::mock(BufferedOutput::class.'[doWrite]')
                ->shouldAllowMockingProtectedMethods()
                ->shouldIgnoreMissing();

        foreach ($this->test->expectedOutput as $i => $output) {
            $mock->shouldReceive('doWrite')
                ->once()
                ->ordered()
                ->with($output, Mockery::any())
                ->andReturnUsing(function () use ($i) {
                    unset($this->test->expectedOutput[$i]);
                });
        }

        return $mock;
    }

    /**
     * Handle the object's destruction.
	 * 处理对象的销毁
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->hasExecuted) {
            return;
        }

        $this->run();
    }
}
