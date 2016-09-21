<?php

namespace Psi\Bundle\Description\Tests\Functional;

use Psi\Bundle\Description\Example\app\AppKernel;

class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    private $container;

    protected function getContainer()
    {
        if ($this->container) {
            return $this->container;
        }
        $kernel = new AppKernel('dev', true);
        $kernel->boot();

        $this->container = $kernel->getContainer();

        return $this->container;
    }
}
