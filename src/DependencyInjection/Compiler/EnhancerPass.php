<?php

namespace Psi\Bundle\Description\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EnhancerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('psi_description.factory')) {
            return;
        }

        $factoryDef = $container->getDefinition('psi_description.factory');
        $enhancerIds = $container->findTaggedServiceIds('psi_description.enhancer');

        $enhancerRefs = [];
        foreach ($enhancerIds as $enhancerId => $attributes) {
            $attributes = $attributes[0];

            if (!isset($attributes['alias'])) {
                throw new \InvalidArgumentException(sprintf(
                    'Description enhancer "%s" has no "alias" attribute in its tag',
                    $enhancerId
                ));
            }

            $alias = $attributes['alias'];

            $enhancerRefs[$alias] = new Reference($enhancerId);
        }

        $enabledEnhancers = $container->getParameter('psi_description.enhancers');
        $diff = array_diff($enabledEnhancers, array_keys($enhancerRefs));

        if ($diff) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown enhancer(s) "%s" specified. Known enhancers: "%s"',
                implode('", "', $diff), 
                implode('", "', array_keys($enhancerRefs))
            ));
        }

        $factoryDef->replaceArgument(0, array_values($enhancerRefs));
    }
}
