<?php

namespace Psi\Bundle\Description\Tests\Functional;

use Psi\Component\Description\Subject;

class DescriptionFactoryTest extends FunctionalTestCase
{
    /**
     * It should return a description for an object.
     */
    public function testDescribe()
    {
        $object = new \stdClass();
        $object->title = 'Hello World';
        $factory = $this->getContainer()->get('psi_description.factory');
        $description = $factory->describe(Subject::createFromObject($object));

        $this->assertEquals('Hello World', $description->get('example.title')->getValue());
    }
}
