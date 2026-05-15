<?php
/**
 * Symfony，组件，Var Dumper，测试，固定装置，Php74
 */

namespace Symfony\Component\VarDumper\Tests\Fixtures;

class Php74
{
    public $p1 = 123;
    public \stdClass $p2;

    public function __construct()
    {
        $this->p2 = new \stdClass();
    }
}
