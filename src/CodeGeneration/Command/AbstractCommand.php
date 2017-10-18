<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class AbstractCommand extends Command
{
    const COMMAND_PREFIX = 'dsm:';

    const OPT_PROJECT_ROOT_NAMESPACE = 'project-root-namespace';
    const OPT_PROJECT_ROOT_NAMESPACE_SHORT = 'r';
    const DEFINITION_PROJECT_ROOT_NAMESPACE = 'The root namespace for the project for which you are building entities. The entities root namespace is suffixed to the end of this';

    const OPT_PROJECT_ROOT_PATH = 'project-root-path';
    const OPT_PROJECT_ROOT_PATH_SHORT = 'p';
    const DEFINITION_PROJECT_ROOT_PATH = 'the filesystem path to the folder for the project root namespace';

    const OPT_ENTITIES_ROOT_NAMESPACE = 'entities-root-namespace';
    const OPT_ENTITIES_ROOT_NAMESPACE_SHORT = 'e';
    const DEFINITION_ENTITIES_ROOT_NAMESPACE = 'The namespace and sub folder in which the Entities are placed. Is suffixed to the project root namespace';
    const DEFAULT_ENTITIES_ROOT_NAMESPACE = 'Entities';

    protected function getEntityManager(): EntityManager
    {
        $entityManager = $this->getHelper('em')->getEntityManager();

        return $entityManager;
    }

    protected function checkAllRequiredOptionsAreNotEmpty(InputInterface $input)
    {
        $errors = [];
        $options = $this->getDefinition()->getOptions();
        foreach ($options as $option) {
            $name = $option->getName();
            $value = $input->getOption($name);
            if ($option->isValueRequired() && ($value === null || $value === '')) {
                $errors[] = sprintf('The required option --%s is not set or is empty', $name);
            }
        }
        if (count($errors)) {
            throw new \InvalidArgumentException(implode("\n\n", $errors));
        }
    }
}
