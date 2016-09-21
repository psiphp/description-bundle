<?php

namespace Psi\Bundle\Description\Command;

use Psi\Component\Description\Schema\Definition;
use Psi\Component\Description\Schema\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugCommand extends Command
{
    private $schema;

    public function __construct(
        Schema $schema
    ) {
        parent::__construct();
        $this->schema = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('psi:debug:description');
        $this->addArgument('descriptor', InputArgument::OPTIONAL, 'Show information for specific descriptor');
        $this->setDescription('List and inspect descriptors');
        $this->setHelp(<<<'EOT'
Invoke with no arguments in order to list all available descriptors:

    $ %command.full_name%

Specify a desacriptor in order to show more information:

    $ %command.full_name% std.title
EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getArgument('descriptor');

        if (null === $key) {
            return $this->listDescriptors($output);
        }

        $descriptor = $this->schema->getDefinition($key);

        return $this->showDescriptor($output, $key, $descriptor);
    }

    private function listDescriptors(OutputInterface $output)
    {
        $output->writeln('<info>List of available descriptors:</info>');
        $table = new Table($output);
        $table->setStyle('compact');
        foreach ($this->schema->getDefinitions() as $key => $definition) {
            $table->addRow([
                sprintf('<comment>%s</comment>', $key),
                $definition->getInfo(),
            ]);
        }

        $table->render();
        $output->writeln('// Specify a descriptor key for more information');
    }

    private function showDescriptor(OutputInterface $output, $key, Definition $definition)
    {
        $data = [
            'key' => $key,
            'info' => $definition->getInfo(),
            'descriptor' => $definition->getClass(),
            'extension' => $definition->getExtensionClass(),
        ];

        $table = new Table($output);
        $table->setStyle('compact');

        foreach ($data as $key => $value) {
            $table->addRow([sprintf('<comment>%s</comment>', $key), $value]);
        }

        $table->render();
    }
}
