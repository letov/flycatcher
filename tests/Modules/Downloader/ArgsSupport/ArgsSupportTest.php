<?php

namespace Letov\Flycatcher\Tests\Modules\Downloader\ArgsSupport;


use DI\DependencyException as DependencyExceptionAlias;
use DI\NotFoundException;
use Exception;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgsSupport;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;
use ReflectionException as ReflectionExceptionAlias;

class ArgsSupportTest extends TestCaseIncludeContainer
{
    /**
     * @throws ReflectionExceptionAlias
     * @throws NotFoundException
     * @throws DependencyExceptionAlias
     */
    public function testArgsSupport()
    {
        $argsSupport = $this->container->make(ArgsSupport::class, array('args' => array(
            'TestMethodName' => 'TestArgValue'
        )));
        $this->assertEquals('TestArgValue', $this->reflectionMethod($argsSupport, 'getArg', ['methodName' => 'getTestMethodName']));
    }
}
