<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AppExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('symfony.xml');

        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);

        if (isset($config['workflow']) && $config['workflow']) {
            $this->buildWorkflow($container, $config['workflow']);
        }
    }

    private function buildWorkflow(ContainerBuilder $container, array $workflows)
    {
        $registryDefintion = $container->getDefinition('workflow.registry');

        foreach ($workflows as $name => $workflow) {
            $definitionDefinition = new Definition('Symfony\Component\Workflow\Definition');
            $definitionDefinition->addMethodCall('addPlaces', [$workflow['places']]);
            foreach ($workflow['transitions'] as $transitionName => $transition) {
                $definitionDefinition->addMethodCall('addTransition', [new Definition('Symfony\Component\Workflow\Transition', [$transitionName, $transition['from'], $transition['to']])]);
            }

            if (isset($workflow['marking_store']['type'])) {
                $markingStoreDefinition = new DefinitionDecorator('workflow.marking_store.'.$workflow['marking_store']['type']);
                foreach ($workflow['marking_store']['arguments'] as $arg) {
                    $markingStoreDefinition->addArgument($arg);
                }
            } else {
                $markingStoreDefinition = new Reference($workflow['marking_store']['service']);
            }

            $workflowDefinition = new DefinitionDecorator('workflow.abstract');
            $workflowDefinition->replaceArgument(0, $definitionDefinition);
            $workflowDefinition->replaceArgument(1, $markingStoreDefinition);
            $workflowDefinition->replaceArgument(3, $name);

            $workflowId = 'workflow.'.$name;

            $container->setDefinition($workflowId, $workflowDefinition);

            foreach ($workflow['supports'] as $supportedClass) {
                $registryDefintion->addMethodCall('add', [new Reference($workflowId), $supportedClass]);
            }
        }
    }
}
