<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Process\Process;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Symfony\Component\Workflow\Dumper\StateMachineGraphvizDumper;
use Symfony\Component\Workflow\StateMachine;

class WorkflowUpdateSvgCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('workflow:build:svg')
            ->setDescription('Build the SVG')
            ->addArgument('service_name', InputArgument::REQUIRED, 'The service name of the workflow (ex workflow.article)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('service_name');

        $workflow = $this->container->get($name);
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

        $svg = preg_replace('/.*<svg/ms', sprintf('<svg class="img-responsive" id="%s"', str_replace('.', '-', $name)), $svg);

        $shortName = explode('.', $name)[1];

        file_put_contents(sprintf('%s/templates/%s/doc.svg.twig', $this->container->getParameter('kernel.project_dir'), $shortName), $svg);

        return 0;
    }
}
