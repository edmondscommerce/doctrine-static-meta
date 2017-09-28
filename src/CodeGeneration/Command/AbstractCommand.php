<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;

class AbstractCommand extends Command
{
    const COMMAND_PREFIX = 'dsm:';
    const TEMPLATE_PATH = __DIR__.'/../../../codeTemplates';


    const ARG_PROJECT_ROOT_NAMESPACE = 'project-root-namespace';
    const ARG_PROJECT_ROOT_NAMESPACE_SHORT = 'prn';
    const DEF_PROJECT_ROOT_NAMESPACE = 'The root namespace for the project for which you are building entities. The entities root namespace is suffixed to the end of this';

    const ARG_PROJECT_ROOT_PATH = 'project-root-path';
    const ARG_PROJECT_ROOT_PATH_SHORT = 'p';
    const DEF_PROJECT_ROOT_PATH = 'the filesystem path to the folder for the project root namespace';

    const ARG_ENTITIES_ROOT_NAMESPACE = 'entities-root-namespace';
    const ARG_ENTITIES_ROOT_NAMESPACE_SHORT = 'ern';
    const DEF_ENTITIES_ROOT_NAMESPACE = 'The namespace and sub folder in which the Entities are placed. Is suffixed to the project root namespace';
    const DEFAULT_ENTITIES_ROOT_NAMESPACE = 'Entities';

    const FIND_ENTITY_NAME='TemplateEntity';
    const FIND_NAMESPACE='TemplateNamespace';


    protected $fs;

    protected function getFilesystem(): Filesystem
    {
        if (null === $this->fs) {
            $this->fs = new Filesystem();
        }

        return $this->fs;
    }

    protected function getEm(): EntityManager
    {
        $em = $this->getHelper('em')->getEntityManager();

        return $em;
    }
}