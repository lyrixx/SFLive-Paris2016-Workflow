<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Symfony\Component\Workflow\Workflow;

class WorkflowUpdateSvgCommand extends ContainerAwareCommand
{
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

        $definition = $this->getContainer()->get($name)->getDefinition();

        $dumper = new GraphvizDumper();

        $dot = $dumper->dump($definition, null, ['node' => ['width' => 1.6]]);

        $process = new Process('dot -Tsvg');
        $process->setInput($dot);
        $process->mustRun();

        $svg = $process->getOutput();

        $svg = preg_replace('/.*<svg/ms', sprintf('<svg class="img-responsive" id="%s"', str_replace('.', '-', $name)), $svg);

        $shortName = explode('.', $name)[1];

        file_put_contents(sprintf('%s/Resources/views/%s/doc.svg.twig', $this->getContainer()->getParameter('kernel.root_dir'), $shortName), $svg);
    }
}
