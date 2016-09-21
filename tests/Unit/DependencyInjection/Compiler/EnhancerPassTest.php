<?php

namespace Psi\Bundle\Description\Tests\Unit\DependencyInjection\Compiler;

use Psi\Bundle\Description\DependencyInjection\Compiler\EnhancerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class EnhancerPassTest extends \PHPUnit_Framework_TestCase
{
    private $pass;

    public function setUp()
    {
        $this->pass = new EnhancerPass();
        $this->container = $this->prophesize(ContainerBuilder::class);
        $this->factoryDef = $this->prophesize(Definition::class);

        $this->container->hasDefinition('psi_description.factory')->willReturn(true);
        $this->container->getDefinition('psi_description.factory')->willReturn(
            $this->factoryDef->reveal()
        );
    }

    /**
     * It should throw an exception if the enhancer tag has no "alias" attribute.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Description enhancer "foobar" has no "alias" attribute in its tag
     */
    public function testThrowExceptionNoAlias()
    {
        $this->container->findTaggedServiceIds('psi_description.enhancer')->willReturn([
            'foobar' => [[]],
        ]);
        $this->pass->process($this->container->reveal());
    }

    /**
     * It should throw an exception if an unknown enhancer is specified.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown enhancer(s) "barbar", "booboo" specified. Known enhancers: "foobar"
     */
    public function testThrowExceptionUnknownEnhancer()
    {
        $this->container->findTaggedServiceIds('psi_description.enhancer')->willReturn([
            'foobar.service.id' => [['alias' => 'foobar']],
        ]);
        $this->container->getParameter('psi_description.enhancers')->willReturn(['barbar', 'booboo', 'foobar']);
        $this->pass->process($this->container->reveal());
    }

    /**
     * It should "inject" the enhancers to the factory.
     */
    public function testReplaceEnhancers()
    {
        $this->container->findTaggedServiceIds('psi_description.enhancer')->willReturn([
            'foobar.service.id' => [['alias' => 'foobar']],
        ]);
        $this->container->getParameter('psi_description.enhancers')->willReturn(['foobar']);
        $this->factoryDef->replaceArgument(0, [
            new Reference('foobar.service.id'),
        ])->shouldBeCalled();
        $this->pass->process($this->container->reveal());
    }
}
