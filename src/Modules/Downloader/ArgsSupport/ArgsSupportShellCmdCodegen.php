<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport;

use Letov\Flycatcher\Modules\ShellCmd\ShellCmdInterface;
use Nette\PhpGenerator\PhpNamespace;
use ReflectionClass;
use ReflectionException;

class ArgsSupportShellCmdCodegen
{
    static function generate()
    {
        $interfaces = get_declared_interfaces();
        array_map(function ($interfaceFile)
        {
            require $interfaceFile;
        }, glob(__DIR__ . '/ArgInterfaces/*.php'));
        $argInterfaces = array_diff(get_declared_interfaces(), $interfaces);
        $argsSupportShellCmdNamespace = new PhpNamespace('Letov\Flycatcher\Modules\Downloader\ArgsSupport');
        $argsSupportShellCmdClass = $argsSupportShellCmdNamespace->addClass("ArgsSupportShellCmd");
        $argsSupportShellCmdClass->setExtends(ArgsSupport::class);
        foreach ($argInterfaces as $argInterface)
        {
            $argsSupportShellCmdClass->addImplement($argInterface);
        }
        $argsSupportShellCmdClass
            ->addProperty('shellCmd')
            ->setType(ShellCmdInterface::class)
            ->setProtected()
            ->addComment("setShellCmdArgs");
        $constructor = $argsSupportShellCmdClass->addMethod('__construct');
        $constructor
            ->addParameter('args')
            ->setType('array');
        $constructor
            ->addParameter('shellCmd')
            ->setType(ShellCmdInterface::class);
        $constructor
            ->addBody('$this->shellCmd = $shellCmd;')
            ->addBody('parent::__construct($args);')
            ->addBody('$this->setShellCmdArgs();');
        foreach ($argInterfaces as $argInterface)
            try {
                $interface = new ReflectionClass($argInterface);
                $interfaceMethods = $interface->getMethods();
                if (1 != count($interfaceMethods)) {
                    continue;
                }
                $interfaceMethod = $interfaceMethods[0];
                $argsSupportShellCmdClass
                    ->addMethod($interfaceMethod->name)
                    ->setReturnType($interfaceMethod->getReturnType())
                    ->setReturnNullable()
                    ->addBody('return $this->getArg(__FUNCTION__);');
            } catch (ReflectionException $e) {
                continue;
            }
        // PhpGenerator does not support abstract classes
        $argsSupportShellCmdNamespace = str_ireplace("class", "abstract class", $argsSupportShellCmdNamespace);
        $argsSupportShellCmdNamespace = str_ireplace("/** setShellCmdArgs */", "abstract protected function setShellCmdArgs();", $argsSupportShellCmdNamespace);
        file_put_contents(__DIR__ . '/ArgsSupportShellCmd.php', "<?php\n// THIS IS CODEGENERATED FILE\n{$argsSupportShellCmdNamespace}");
    }
}