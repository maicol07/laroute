<?php

namespace Dogado\Laroute\Compilers;

use Mockery;

class TemplateCompilerTest extends \PhpUnit\Framework\TestCase
{
    protected $compiler;

    public function setUp()
    {
        parent::setUp();

        $this->compiler = new TemplateCompiler();
    }

    public function testItIsOfTheCorrectInterface()
    {
        $this->assertInstanceOf(
            'Dogado\Laroute\Compilers\CompilerInterface',
            $this->compiler
        );
    }

    public function testItCanCompileAString()
    {
        $template = 'Hello $YOU$, my name is $ME$.';
        $data     = ['you' => 'Stranger', 'me' => 'Aaron'];
        $expected = "Hello Stranger, my name is Aaron.";

        $this->assertSame($expected, $this->compiler->compile($template, $data));
    }

    public function tearDown()
    {
        Mockery::close();
    }

    protected function mock($class)
    {
        $mock = Mockery::mock($class);
        $this->app->instance($class, $mock);
        return $mock;
    }
}