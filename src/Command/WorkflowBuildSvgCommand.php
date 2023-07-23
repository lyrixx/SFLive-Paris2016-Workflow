<?php

namespace App\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Symfony\Component\Workflow\Dumper\StateMachineGraphvizDumper;
use Symfony\Component\Workflow\StateMachine;

#[AsCommand(
    name: 'workflow:build:svg',
    description: 'Updates the SVG files for the workflows',
)]
class WorkflowBuildSvgCommand extends Command
{
    public function __construct(
        private readonly ContainerInterface $workflows,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('workflow:build:svg')
            ->addArgument('name', InputArgument::REQUIRED, 'The workflow name (ex: article)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        $workflow = $this->workflows->get($name);
        $definition = $workflow->getDefinition();

        if ($workflow instanceof StateMachine) {
            $dumper = new StateMachineGraphvizDumper();
        } else {
            $dumper = new GraphvizDumper();
        }

        $dot = $dumper->dump($definition, null, ['node' => ['width' => 1.6]]);

        $process = new Process(['dot', '-Tsvg']);
        $process->setInput($dot);
        $process->mustRun();

        $svg = $process->getOutput();

        $svg = preg_replace('/.*<svg/ms', sprintf('<svg class="img-responsive" id="%s"', str_replace('.', '-', (string) $name)), $svg);

        file_put_contents(sprintf('%s/templates/%s/doc.svg.twig', $this->projectDir, $name), $svg);

        return Command::SUCCESS;
    }
}
