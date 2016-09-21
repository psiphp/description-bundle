<?php

namespace Psi\Bundle\Description\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PsiDescriptionExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('twig.xml');
        $loader->load('console.xml');

        $factoryDef = $container->getDefinition('psi_description.factory');

        if ($config['schema']['enabled']) {
            $factoryDef->replaceArgument(1, new Reference('psi_description.schema'));
        }

        $container->setParameter('psi_description.enhancers', $config['enhancers']);
        $container->setParameter('psi_description.schema.extensions', $config['schema']['extensions']);
    }
}
