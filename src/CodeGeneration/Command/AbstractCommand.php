<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;

class AbstractCommand extends Command
{
    const COMMAND_PREFIX = 'dsm:';

    const ARG_PROJECT_ROOT_NAMESPACE = 'project-root-namespace';
    const ARG_PROJECT_ROOT_NAMESPACE_SHORT = 'prn';
    const DEFINITION_PROJECT_ROOT_NAMESPACE = 'The root namespace for the project for which you are building entities. The entities root namespace is suffixed to the end of this';

    const ARG_PROJECT_ROOT_PATH = 'project-root-path';
    const ARG_PROJECT_ROOT_PATH_SHORT = 'p';
    const DEFINITION_PROJECT_ROOT_PATH = 'the filesystem path to the folder for the project root namespace';

    const ARG_ENTITIES_ROOT_NAMESPACE = 'entities-root-namespace';
    const ARG_ENTITIES_ROOT_NAMESPACE_SHORT = 'ern';
    const DEFINITION_ENTITIES_ROOT_NAMESPACE = 'The namespace and sub folder in which the Entities are placed. Is suffixed to the project root namespace';
    const DEFAULT_ENTITIES_ROOT_NAMESPACE = 'Entities';




    protected function getEm(): EntityManager
    {
        $em = $this->getHelper('em')->getEntityManager();

        return $em;
    }
}
