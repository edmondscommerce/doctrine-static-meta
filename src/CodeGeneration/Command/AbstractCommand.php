<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class AbstractCommand extends Command
{
    public const COMMAND_PREFIX = 'dsm:';

    public const OPT_PROJECT_ROOT_NAMESPACE            = 'project-root-namespace';
    public const OPT_PROJECT_ROOT_NAMESPACE_SHORT      = 'r';
    public const DEFINITION_PROJECT_ROOT_NAMESPACE     = 'The root namespace for the project for which you are building entities. The entities root namespace is suffixed to the end of this';
    public const DEFAULT_PROJECT_ROOT_NAMESPACE_METHOD = 'getProjectRootNamespace';

    public const OPT_PROJECT_ROOT_PATH            = 'project-root-path';
    public const OPT_PROJECT_ROOT_PATH_SHORT      = 'p';
    public const DEFINITION_PROJECT_ROOT_PATH     = 'the filesystem path to the folder for the project. '
                                                    .'This would be the folder that generally has a subfolder `src` '
                                                    .'and a sub folder `tests`';
    public const DEFAULT_PROJECT_ROOT_PATH_METHOD = 'getProjectRootPath';

    public const OPT_ENTITIES_ROOT_FOLDER        = 'entities-root-folder';
    public const OPT_ENTITIES_ROOT_FOLDER_SHORT  = 'e';
    public const DEFINITION_ENTITIES_ROOT_FOLDER = 'The namespace segment or sub folder in which the Entities are '
                                                   .'placed. Is suffixed to the project root namespace, '
                                                   .'defaults to `Entities`';
    public const DEFAULT_ENTITIES_ROOT_FOLDER    = 'Entities';

    public const OPT_SRC_SUBFOLDER        = 'src-sub-folder';
    public const OPT_SRC_SUBFOLDER_SHORT  = 's';
    public const DEFINITION_SRC_SUBFOLDER = 'The name of the subdfolder that contains sources. '
                                            .'Generally this is `src` which is the default';
    public const DEFAULT_SRC_SUBFOLDER    = 'src';

    public const OPT_TEST_SUBFOLDER        = 'test-sub-folder';
    public const OPT_TEST_SUBFOLDER_SHORT  = 't';
    public const DEFINITION_TEST_SUBFOLDER = 'The name of the subdfolder that contains tests. '
                                             .'Generally this is `tests` which is the default';
    public const DEFAULT_TEST_SUBFOLDER    = 'tests';

    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;

    /**
     * AbstractCommand constructor.
     *
     * @param NamespaceHelper $namespaceHelper
     * @param null|string     $name
     *
     * @throws DoctrineStaticMetaException
     */
    public function __construct(NamespaceHelper $namespaceHelper, ?string $name = null)
    {
        $this->namespaceHelper = $namespaceHelper;
        try {
            parent::__construct($name);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in '.__METHOD__,
                $e->getCode(),
                $e
            );
        }
    }


    /**
     * @return EntityManager
     * @throws DoctrineStaticMetaException
     */
    protected function getEntityManager(): EntityManager
    {
        try {
            return $this->getHelper('em')->getEntityManager();
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__, $e->getCode(), $e);
        }
    }

    protected function checkValueForEquals($value, string $name, array &$errors)
    {
        if (\is_string($value) && '' !== $value) {
            if (0 === strpos($value, '=')) {
                $errors[] = 'Value for '.$name.' is '.$value
                            .' and starts with =, if use short options, you should not use an = sign';
            }
        }
    }

    protected function checkOptionRequired(InputOption $option, $value, string $name, array &$errors)
    {
        if ($option->isValueRequired() && (
                $value === null
                || $value === ''
                || ($option->isArray() && $value === [])
            )
        ) {
            $errors[] = sprintf('The required option --%s is not set or is empty', $name);
        }
    }

    /**
     * @param InputInterface $input
     *
     * @throws DoctrineStaticMetaException
     */
    protected function checkOptions(InputInterface $input)
    {
        $errors  = [];
        $options = $this->getDefinition()->getOptions();
        foreach ($options as $option) {
            $name  = $option->getName();
            $value = $input->getOption($name);
            $this->checkOptionRequired($option, $value, $name, $errors);
            if (\is_array($value)) {
                foreach ($value as $v) {
                    $this->checkValueForEquals($v, $name, $errors);
                }
                continue;
            }
            $this->checkValueForEquals($value, $name, $errors);
        }
        if (count($errors) > 0) {
            throw new DoctrineStaticMetaException(implode("\n\n", $errors));
        }
    }

    /**
     * Getter for self::DEFAULT_PROJECT_ROOT_PATH
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    protected function getProjectRootPath(): string
    {
        try {
            return Config::getProjectRootDirectory();
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__, $e->getCode(), $e);
        }
    }

    /**
     * @param string $dirForNamespace
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    protected function getProjectRootNamespace(string $dirForNamespace = 'src'): string
    {
        return (new NamespaceHelper())->getProjectRootNamespaceFromComposerJson($dirForNamespace);
    }

    /**
     * @return InputOption
     * @throws DoctrineStaticMetaException
     */
    protected function getProjectRootPathOption(): InputOption
    {
        try {
            return new InputOption(
                self::OPT_PROJECT_ROOT_PATH,
                self::OPT_PROJECT_ROOT_PATH_SHORT,
                InputOption::VALUE_OPTIONAL,
                self::DEFINITION_PROJECT_ROOT_PATH,
                $this->{self::DEFAULT_PROJECT_ROOT_PATH_METHOD}()
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception getting option', $e->getCode(), $e);
        }
    }

    /**
     * @return InputOption
     * @throws DoctrineStaticMetaException
     */
    protected function getProjectRootNamespaceOption(): InputOption
    {
        try {
            return new InputOption(
                self::OPT_PROJECT_ROOT_NAMESPACE,
                self::OPT_PROJECT_ROOT_NAMESPACE_SHORT,
                InputOption::VALUE_REQUIRED,
                self::DEFINITION_PROJECT_ROOT_NAMESPACE,
                \call_user_func([$this, self::DEFAULT_PROJECT_ROOT_NAMESPACE_METHOD])
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception getting option', $e->getCode(), $e);
        }
    }

    /**
     * @return InputOption
     * @throws DoctrineStaticMetaException
     */
    protected function getProjectEntitiesRootNamespaceOption(): InputOption
    {
        try {
            return new InputOption(
                self::OPT_ENTITIES_ROOT_FOLDER,
                self::OPT_ENTITIES_ROOT_FOLDER_SHORT,
                InputOption::VALUE_OPTIONAL,
                self::DEFINITION_ENTITIES_ROOT_FOLDER,
                self::DEFAULT_ENTITIES_ROOT_FOLDER
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception getting option', $e->getCode(), $e);
        }
    }

    /**
     * @return InputOption
     * @throws DoctrineStaticMetaException
     */
    protected function getSrcSubfolderOption(): InputOption
    {
        try {
            return new InputOption(
                self::OPT_SRC_SUBFOLDER,
                self::OPT_SRC_SUBFOLDER_SHORT,
                InputOption::VALUE_REQUIRED,
                self::DEFINITION_SRC_SUBFOLDER,
                self::DEFAULT_SRC_SUBFOLDER
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception getting option', $e->getCode(), $e);
        }
    }

    /**
     * @return InputOption
     * @throws DoctrineStaticMetaException
     */
    protected function getTestSubFolderOption(): InputOption
    {
        try {
            return new InputOption(
                self::OPT_TEST_SUBFOLDER,
                self::OPT_TEST_SUBFOLDER_SHORT,
                InputOption::VALUE_REQUIRED,
                self::DEFINITION_TEST_SUBFOLDER,
                self::DEFAULT_TEST_SUBFOLDER
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception getting option', $e->getCode(), $e);
        }
    }
}
