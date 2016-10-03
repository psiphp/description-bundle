<?php

namespace Psi\Bundle\Description\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DescriptionFactoryPass implements CompilerPassInterface
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
        $resolverIds = $container->findTaggedServiceIds('psi_description.subject_resolver');

        $refs = [
            'enhancer' => [],
            'subject_resolver' => [],
        ];

        foreach ([
            'enhancer' => $enhancerIds,
            'subject_resolver' => $resolverIds,
        ] as $key => $serviceIds) {
            $humanKey = str_replace('_', ' ', $key);
            foreach ($serviceIds as $serviceId => $attributes) {
                $attributes = $attributes[0];

                if (!isset($attributes['alias'])) {
                    throw new \InvalidArgumentException(sprintf(
                        'Description %s "%s" has no "alias" attribute in its tag',
                        $humanKey,
                        $serviceId
                    ));
                }

                $alias = $attributes['alias'];

                $refs[$key][$alias] = new Reference($serviceId);
            }

            $enabled = $container->getParameter(sprintf('psi_description.%ss', $key));
            $diff = array_diff($enabled, array_keys($refs[$key]));

            if ($diff) {
                throw new \InvalidArgumentException(sprintf(
                    'Unknown %s(s) "%s" specified. Known %s(s): "%s"',
                    $humanKey,
                    implode('", "', $diff),
                    $humanKey,
                    implode('", "', array_keys($refs[$key]))
                ));
            }

            $orderedRefs = [];

            foreach ($enabled as $enabledAlias) {
                $orderedRefs[$enabledAlias] = $refs[$key][$enabledAlias];
            }

            $refs[$key] = $orderedRefs;
        }

        $factoryDef->replaceArgument(0, array_values($refs['enhancer']));
        $factoryDef->replaceArgument(2, array_values($refs['subject_resolver']));
    }
}
