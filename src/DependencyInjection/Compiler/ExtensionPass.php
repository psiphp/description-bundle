<?php

namespace Psi\Bundle\Description\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExtensionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('psi_description.schema')) {
            return;
        }

        $schemaDef = $container->getDefinition('psi_description.schema');
        $extensionIds = $container->findTaggedServiceIds('psi_description.schema_extension');

        $extensionRefs = [];
        foreach ($extensionIds as $extensionId => $attributes) {
            $attributes = $attributes[0];

            if (!isset($attributes['alias'])) {
                throw new \InvalidArgumentException(sprintf(
                    'Description schema extension "%s" has no "alias" attribute in its tag',
                    $extensionId
                ));
            }

            $alias = $attributes['alias'];

            $extensionRefs[$alias] = new Reference($extensionId);
        }

        $enabledEnhancers = $container->getParameter('psi_description.schema.extensions');
        $diff = array_diff($enabledEnhancers, array_keys($extensionRefs));

        if ($diff) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown schema extension(s) "%s" specified. Known extensions: "%s"',
                implode('", "', $diff), implode('", "', array_keys($extensionRefs))
            ));
        }

        $schemaDef->replaceArgument(0, array_values($extensionRefs));
    }
}
