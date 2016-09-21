<?php

namespace Psi\Bundle\Description;

use Psi\Bundle\Description\DependencyInjection\Compiler\EnhancerPass;
use Psi\Bundle\Description\DependencyInjection\Compiler\ExtensionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PsiDescriptionBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new EnhancerPass());
        $container->addCompilerPass(new ExtensionPass());
    }
}
