<?php

namespace Psi\Bundle\Description\Tests\Functional\Command;

use Psi\Bundle\Description\Tests\Functional\FunctionalTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class DebugCommandTest extends FunctionalTestCase
{
    public function testCommandList()
    {
        $output = $this->runCommand();
        $this->assertContains('std.title', $output->fetch());
    }

    public function testCommandShow()
    {
        $output = $this->runCommand([
            'descriptor' => 'std.title',
        ]);
        $this->assertContains('StringDescriptor', $output->fetch());
    }

    private function runCommand(array $input = [])
    {
        $output = new BufferedOutput();
        $input = new ArrayInput($input);
        $command = $this->getContainer()->get('psi_description.command.debug');
        $command->run($input, $output);

        return $output;
    }
}
