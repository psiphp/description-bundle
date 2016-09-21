<?php

namespace Psi\Bundle\Description\Tests\Unit\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Psi\Bundle\Description\DependencyInjection\Compiler\ExtensionPass;
use Symfony\Component\DependencyInjection\Reference;
use Prophecy\Argument;

class ExtensionPassTest extends \PHPUnit_Framework_TestCase
{
    private $pass;

    public function setUp()
    {
        $this->pass = new ExtensionPass();
        $this->container = $this->prophesize(ContainerBuilder::class);
        $this->schemaDef = $this->prophesize(Definition::class);

        $this->container->hasDefinition('psi_description.schema')->willReturn(true);
        $this->container->getDefinition('psi_description.schema')->willReturn(
            $this->schemaDef->reveal()
        );
    }

    /**
     * It should throw an exception if the extension tag has no "alias" attribute.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Description schema extension "foobar" has no "alias" attribute in its tag
     */
    public function testThrowExceptionNoAlias()
    {
        $this->container->findTaggedServiceIds('psi_description.schema_extension')->willReturn([
            'foobar' => [[]],
        ]);
        $this->pass->process($this->container->reveal());
    }


    /**
     * It should throw an exception if an unknown extension is specified.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown schema extension(s) "barbar", "booboo" specified. Known extensions: "foobar"
     */
    public function testThrowExceptionUnknownExtension()
    {
        $this->container->findTaggedServiceIds('psi_description.schema_extension')->willReturn([
            'foobar.service.id' => [[ 'alias' => 'foobar']],
        ]);
        $this->container->getParameter('psi_description.schema.extensions')->willReturn([ 'barbar', 'booboo', 'foobar' ]);
        $this->pass->process($this->container->reveal());
    }

    /**
     * It should "inject" the extensions to the factory.
     */
    public function testReplaceExtensions()
    {
        $this->container->findTaggedServiceIds('psi_description.schema_extension')->willReturn([
            'foobar.service.id' => [[ 'alias' => 'foobar']],
        ]);
        $this->container->getParameter('psi_description.schema.extensions')->willReturn([ 'foobar' ]);
        $this->schemaDef->replaceArgument(0, [
            new Reference('foobar.service.id')
        ])->shouldBeCalled();

        $this->pass->process($this->container->reveal());
    }
}
