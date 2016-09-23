<?php

namespace Psi\Bundle\Description\Tests\Unit\DependencyInjection\Compiler;

use Psi\Bundle\Description\DependencyInjection\Compiler\DescriptionFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DescriptionFactoryPassTest extends \PHPUnit_Framework_TestCase
{
    private $pass;

    public function setUp()
    {
        $this->pass = new DescriptionFactoryPass();
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
        $this->container->findTaggedServiceIds('psi_description.subject_resolver')->willReturn([]);
        $this->pass->process($this->container->reveal());
    }

    /**
     * It should throw an exception if an unknown enhancer is specified.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown enhancer(s) "barbar", "booboo" specified. Known enhancer(s): "foobar"
     */
    public function testThrowExceptionUnknownEnhancer()
    {
        $this->container->findTaggedServiceIds('psi_description.enhancer')->willReturn([
            'foobar.service.id' => [['alias' => 'foobar']],
        ]);
        $this->container->findTaggedServiceIds('psi_description.subject_resolver')->willReturn([]);
        $this->container->getParameter('psi_description.enhancers')->willReturn(['barbar', 'booboo', 'foobar']);
        $this->pass->process($this->container->reveal());
    }

    /**
     * It should throw an exception if an unknown subject resolver is specified.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown subject resolver(s) "barbar", "booboo" specified. Known subject resolver(s): "foobar"
     */
    public function testThrowExceptionUnknownResolver()
    {
        $this->container->findTaggedServiceIds('psi_description.subject_resolver')->willReturn([
            'foobar.service.id' => [['alias' => 'foobar']],
        ]);
        $this->container->findTaggedServiceIds('psi_description.enhancer')->willReturn([]);
        $this->container->getParameter('psi_description.enhancers')->willReturn([]);
        $this->container->getParameter('psi_description.subject_resolvers')->willReturn(['barbar', 'booboo', 'foobar']);
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
        $this->container->findTaggedServiceIds('psi_description.subject_resolver')->willReturn([]);
        $this->container->getParameter('psi_description.enhancers')->willReturn(['foobar']);
        $this->container->getParameter('psi_description.subject_resolvers')->willReturn([]);

        $this->factoryDef->replaceArgument(2, [])->shouldBeCalled();
        $this->factoryDef->replaceArgument(0, [
            new Reference('foobar.service.id'),
        ])->shouldBeCalled();

        $this->pass->process($this->container->reveal());
    }

    /**
     * It should add subject resolvers.
     */
    public function testSubjectResolvers()
    {
        $this->container->findTaggedServiceIds('psi_description.subject_resolver')->willReturn([
            'foobar.service.id' => [['alias' => 'foobar']],
        ]);
        $this->container->findTaggedServiceIds('psi_description.enhancer')->willReturn([]);
        $this->container->getParameter('psi_description.enhancers')->willReturn([]);
        $this->container->getParameter('psi_description.subject_resolvers')->willReturn(['foobar']);

        $this->factoryDef->replaceArgument(0, [])->shouldBeCalled();
        $this->factoryDef->replaceArgument(2, [
            new Reference('foobar.service.id'),
        ])->shouldBeCalled();

        $this->pass->process($this->container->reveal());
    }
}
