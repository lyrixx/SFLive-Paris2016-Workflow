<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;

class WorkflowUpdateSvgCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('workflow:build:svg')
            ->setDescription('Build the SVG')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $workflow = $this->getContainer()->get('workflow.article');
        $definition = $this->getProperty($workflow, 'definition');

        $dumper = new GraphvizDumper();

        $dot = $dumper->dump($definition, null, ['node' => ['width' => 1.6]]);

        $process = new Process('dot -Tsvg');
        $process->setInput($dot);
        $process->mustRun();

        $svg = $process->getOutput();

        $svg = preg_replace('/.*<svg/ms', '<svg class="img-responsive" id="article-workflow"', $svg);

        file_put_contents(sprintf('%s/Resources/views/doc/article-workflow.svg.twig', $this->getContainer()->getParameter('kernel.root_dir')), $svg);
    }

    private function getProperty($object, $property)
    {
        $reflectionProperty = new \ReflectionProperty(get_class($object), $property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }
}
