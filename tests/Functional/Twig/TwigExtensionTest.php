<?php

namespace Psi\Bundle\Description\Tests\Functional\Twig;

use Psi\Bundle\Description\Tests\Functional\FunctionalTestCase;

class TwigExtensionTest extends FunctionalTestCase
{
    public function testTwigDescribeClass()
    {
        $twig = $this->getContainer()->get('twig');
        $result = $twig->render('describe_class.html.twig');
        $this->assertEquals('<h1>Foobar</h1>', trim($result));
    }

    public function testTwigDescribeObject()
    {
        $object = new \stdClass();
        $object->title = 'Bar Boo';
        $twig = $this->getContainer()->get('twig');
        $result = $twig->render('describe_object.html.twig', [
            'object' => $object,
        ]);
        $this->assertEquals('<h1>Bar Boo</h1>', trim($result));
    }
}
