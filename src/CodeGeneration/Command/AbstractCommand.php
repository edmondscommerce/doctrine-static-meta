<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class AbstractCommand extends Command
{
    const COMMAND_PREFIX = 'dsm:';

    const OPT_PROJECT_ROOT_NAMESPACE            = 'project-root-namespace';
    const OPT_PROJECT_ROOT_NAMESPACE_SHORT      = 'r';
    const DEFINITION_PROJECT_ROOT_NAMESPACE     = 'The root namespace for the project for which you are building entities. The entities root namespace is suffixed to the end of this';
    const DEFAULT_PROJECT_ROOT_NAMESPACE_METHOD = 'getProjectRootNamespace';

    const OPT_PROJECT_ROOT_PATH            = 'project-root-path';
    const OPT_PROJECT_ROOT_PATH_SHORT      = 'p';
    const DEFINITION_PROJECT_ROOT_PATH     = 'the filesystem path to the folder for the project. This would be the folder that generally has a subfolder `src` and a sub folder `tests`';
    const DEFAULT_PROJECT_ROOT_PATH_METHOD = 'getProjectRootPath';

    const OPT_ENTITIES_ROOT_NAMESPACE        = 'entities-root-namespace';
    const OPT_ENTITIES_ROOT_NAMESPACE_SHORT  = 'e';
    const DEFINITION_ENTITIES_ROOT_NAMESPACE = 'The namespace and sub folder in which the Entities are placed. Is suffixed to the project root namespace, defaults to `Entities`';
    const DEFAULT_ENTITIES_ROOT_NAMESPACE    = 'Entities';

    const OPT_SRC_SUBFOLDER        = 'src-sub-folder';
    const OPT_SRC_SUBFOLDER_SHORT  = 's';
    const DEFINITION_SRC_SUBFOLDER = 'The name of the subdfolder that contains sources. Generally this is `src` which is the default';
    const DEFAULT_SRC_SUBFOLDER    = 'src';

    const OPT_TEST_SUBFOLDER        = 'test-sub-folder';
    const OPT_TEST_SUBFOLDER_SHORT  = 't';
    const DEFINITION_TEST_SUBFOLDER = 'The name of the subdfolder that contains tests. Generally this is `tests` which is the default';
    const DEFAULT_TEST_SUBFOLDER    = 'tests';

    protected function getEntityManager(): EntityManager
    {
        $entityManager = $this->getHelper('em')->getEntityManager();

        return $entityManager;
    }

    protected function checkAllRequiredOptionsAreNotEmpty(InputInterface $input)
    {
        $errors  = [];
        $options = $this->getDefinition()->getOptions();
        foreach ($options as $option) {
            $name  = $option->getName();
            $value = $input->getOption($name);
            if ($option->isValueRequired() && (
                    $value === null
                    || $value === ''
                    || ($option->isArray() && $value === [])
                )
            ) {
                $errors[] = sprintf('The required option --%s is not set or is empty', $name);
            }
        }
        if (count($errors)) {
            throw new \InvalidArgumentException(implode("\n\n", $errors));
        }
    }

    /**
     * Getter for self::DEFAULT_PROJECT_ROOT_PATH
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getProjectRootPath(): string
    {
        return Config::getProjectRootDirectory();
    }

    /**
     * @param string $dir
     *
     * @return string
     * @throws \Exception
     */
    protected function getProjectRootNamespace(string $dir = 'src'): string
    {
        return (new NamespaceHelper())->getProjectRootNamespaceFromComposerJson($dir);
    }

    protected function getProjectRootPathOption(): InputOption
    {
        return new InputOption(
            AbstractCommand::OPT_PROJECT_ROOT_PATH,
            AbstractCommand::OPT_PROJECT_ROOT_PATH_SHORT,
            InputOption::VALUE_OPTIONAL,
            AbstractCommand::DEFINITION_PROJECT_ROOT_PATH,
            call_user_func([$this, AbstractCommand::DEFAULT_PROJECT_ROOT_PATH_METHOD])
        );
    }

    protected function getProjectRootNamespaceOption(): InputOption
    {
        return new InputOption(
            AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE,
            AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT,
            InputOption::VALUE_REQUIRED,
            AbstractCommand::DEFINITION_PROJECT_ROOT_NAMESPACE,
            call_user_func([$this, AbstractCommand::DEFAULT_PROJECT_ROOT_NAMESPACE_METHOD])
        );
    }

    protected function getProjectEntitiesRootNamespaceOption(): InputOption
    {
        return new InputOption(
            AbstractCommand::OPT_ENTITIES_ROOT_NAMESPACE,
            AbstractCommand::OPT_ENTITIES_ROOT_NAMESPACE_SHORT,
            InputOption::VALUE_OPTIONAL,
            AbstractCommand::DEFINITION_ENTITIES_ROOT_NAMESPACE,
            AbstractCommand::DEFINITION_ENTITIES_ROOT_NAMESPACE
        );
    }
}
