<?php

namespace Psi\Bundle\Description\Example\src;

use Psi\Component\Description\Schema\ExtensionInterface;
use Psi\Component\Description\Schema\Builder;
use Psi\Component\Description\Descriptor\StringDescriptor;

class TestExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildSchema(Builder $builder)
    {
        $builder->add('title', StringDescriptor::class, 'Example title');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'example';
    }
}
