<?php
/**
 * Symfony，组件，Var Dumper，测试，固定装置，发生器演示
 */

namespace Symfony\Component\VarDumper\Tests\Fixtures;

class GeneratorDemo
{
    public static function foo()
    {
        yield 1;
    }

    public function baz()
    {
        yield from bar();
    }
}

function bar()
{
    yield from GeneratorDemo::foo();
}
