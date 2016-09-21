<?php

namespace Psi\Bundle\Description\Example\src;

use Psi\Component\Description\EnhancerInterface;
use Psi\Component\Description\Descriptor\StringDescriptor;
use Psi\Component\Description\Descriptor\ClassDescriptor;
use Psi\Component\Description\DescriptionInterface;
use Psi\Component\Description\Subject;

class TestEnhancer implements EnhancerInterface
{
    public function enhanceFromClass(DescriptionInterface $description, \ReflectionClass $class)
    {
        $description->set(new ClassDescriptor('std.class', $class));
        $description->set(new StringDescriptor('std.title', 'Foobar'));
    }

    public function enhanceFromObject(DescriptionInterface $description, Subject $subject)
    {
        $description->set(new StringDescriptor('example.title', $subject->getObject()->title));
    }

    public function supports(Subject $subject)
    {
        if ($subject->getClass()->getName() !== \stdClass::class) {
            return false;
        }

        return true;
    }
}
