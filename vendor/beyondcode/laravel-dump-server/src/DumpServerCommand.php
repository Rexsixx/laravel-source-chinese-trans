<?php
/**
 * BeyondCode，转储服务器，转储服务器命令
 */

namespace BeyondCode\DumpServer;

use Illuminate\Console\Command;

use InvalidArgumentException;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\DumpServer;
use Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;
use Symfony\Component\VarDumper\Command\Descriptor\HtmlDescriptor;

class DumpServerCommand extends Command
{
    /**
     * The console command name.
	 * 控制台命令名
     *
     * @var string
     */
    protected $signature = 'dump-server {--format=cli : The output format (cli,html).}';

    /**
     * The console command description.
	 * 控制台命令描述
     *
     * @var string
     */
    protected $description = 'Start the dump server to collect dump information.';

    /**
     * The Dump server.
	 * 转储服务器
     *
     * @var \Symfony\Component\VarDumper\Server\DumpServer
     */
    protected $server;

    /**
     * DumpServerCommand constructor.
	 * DumpServerCommand构造函数
     *
     * @param  \Symfony\Component\VarDumper\Server\DumpServer  $server
     * @return void
     */
    public function __construct(DumpServer $server)
    {
        $this->server = $server;

        parent::__construct();
    }

    /**
     * Handle the command.
	 * 处理命令
     *
     * @return void
     */
    public function handle()
    {
        switch ($format = $this->option('format')) {
            case 'cli':
                $descriptor = new CliDescriptor(new CliDumper);
                break;
            case 'html':
                $descriptor = new HtmlDescriptor(new HtmlDumper);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Unsupported format "%s".', $format));
        }

        $io = new SymfonyStyle($this->input, $this->output);

        $errorIo = $io->getErrorStyle();
        $errorIo->title('Laravel Var Dump Server');

        $this->server->start();

        $errorIo->success(sprintf('Server listening on %s', $this->server->getHost()));
        $errorIo->comment('Quit the server with CONTROL-C.');

        $this->server->listen(function (Data $data, array $context, int $clientId) use ($descriptor, $io) {
            $descriptor->describe($io, $data, $context, $clientId);
        });
    }
}
