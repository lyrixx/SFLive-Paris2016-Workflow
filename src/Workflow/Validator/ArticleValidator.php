<?php

namespace App\Workflow\Validator;

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Exception\InvalidDefinitionException;
use Symfony\Component\Workflow\Validator\DefinitionValidatorInterface;

class ArticleValidator implements DefinitionValidatorInterface
{
    public function validate(Definition $definition, string $name): void
    {
        if (!$definition->getMetadataStore()->getMetadata('title')) {
            throw new InvalidDefinitionException(sprintf('The workflow metadata title is missing in Workflow "%s".', $name));
        }
    }
}
