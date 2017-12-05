<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractGenerator
{
    const TEMPLATE_PATH = __DIR__ . '/../../../codeTemplates';

    const ENTITY_TEMPLATE_PATH = self::TEMPLATE_PATH . '/src/Entities/TemplateEntity.php';

    const ENTITY_TEST_TEMPLATE_PATH = self::TEMPLATE_PATH . '/tests/Entities/TemplateEntityTest.php';

    const ABSTRACT_ENTITY_TEST_TEMPLATE_PATH = self::TEMPLATE_PATH . '/tests/Entities/AbstractEntityTest.php';

    const RELATIONS_TEMPLATE_PATH = self::TEMPLATE_PATH . '/src/Entities/Traits/Relations/TemplateEntity';

    const FIND_ENTITY_NAME = 'TemplateEntity';

    const FIND_ENTITY_NAME_PLURAL = 'TemplateEntities';

    const FIND_NAMESPACE = 'TemplateNamespace\\Entities';


    /**
     * @var string
     */
    protected
        $projectRootNamespace = '',
        $pathToProjectSrcRoot = '',
        $entitiesFolderName = '',
        $srcSubFolderName = '',
        $testSubFolderName = '';

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    public function __construct(
        string $projectRootNamespace,
        string $pathToProjectSrcRoot,
        string $entitiesFolderName = AbstractCommand::DEFAULT_ENTITIES_ROOT_NAMESPACE,
        string $srcSubFolderName = AbstractCommand::DEFAULT_SRC_SUBFOLDER,
        string $testSubFolderName = AbstractCommand::DEFAULT_TEST_SUBFOLDER
    )
    {
        $this->projectRootNamespace = $projectRootNamespace;
        $this->pathToProjectSrcRoot = $pathToProjectSrcRoot;
        $this->entitiesFolderName = $entitiesFolderName;
        $this->srcSubFolderName = $srcSubFolderName;
        $this->testSubFolderName = $testSubFolderName;
    }

    protected function getFilesystem(): Filesystem
    {
        if (null === $this->fileSystem) {
            $this->fileSystem = new Filesystem();
        }

        return $this->fileSystem;
    }

    /**
     * From the fully qualified name, parse out:
     *  - class name,
     *  - namespace
     *  - the namespace parts not including the project root namespace
     *
     * @param string $fqn
     *
     * @return array [$className,$namespace,$subDirectories]
     */
    protected function parseFullyQualifiedName(string $fqn)
    {
        $fqnParts = explode('\\', $fqn);
        $className = array_pop($fqnParts);
        $namespace = implode('\\', $fqnParts);
        $rootParts = explode('\\', $this->projectRootNamespace);
        $subDirectories = array_diff($fqnParts, $rootParts);

        return [
            $className,
            $namespace,
            $subDirectories,
        ];
    }

    protected function createSubDirectoriesAndGetPath(array $subDirectories): string
    {
        $fs = $this->getFilesystem();
        $path = $this->pathToProjectSrcRoot;
        if (!$fs->exists($path)) {
            throw new \Exception("path to project root $path does not exist");
        }
        foreach ($subDirectories as $sd) {
            $path .= "/$sd";
            $fs->mkdir($path);
        }

        return realpath($path);
    }

    protected function copyTemplateDirectoryAndGetPath(
        string $templatePath,
        string $destPath
    ): string
    {
        $fs = $this->getFilesystem();
        $templatePath = realpath($templatePath);
        $relativeDestPath = $fs->makePathRelative($destPath, $this->pathToProjectSrcRoot);
        $subDirectories = explode('/', $relativeDestPath);
        $path = $this->createSubDirectoriesAndGetPath($subDirectories);
        $fs->mirror($templatePath, $path);

        return $path;
    }

    protected function copyTemplateAndGetPath(
        string $templatePath,
        string $destinationFileName,
        array $subDirectories,
        string $srcOrTest = AbstractCommand::DEFAULT_SRC_SUBFOLDER
    ): string
    {
        array_unshift($subDirectories, $srcOrTest);
        $path = $this->createSubDirectoriesAndGetPath($subDirectories);
        if (false === strpos($destinationFileName, '.php')) {
            $destinationFileName = "$destinationFileName.php";
        }
        $filePath = "$path/$destinationFileName";
        $this->getFilesystem()->copy($templatePath, $filePath);

        return $filePath;
    }

    protected function findReplace(string $find, string $replace, string $filePath): AbstractGenerator
    {
        $contents = file_get_contents($filePath);
        $contents = str_replace($find, $replace, $contents);
        file_put_contents($filePath, $contents);

        return $this;
    }

    protected function replaceEntityName(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(self::FIND_ENTITY_NAME, $replacement, $filePath);
        $this->findReplace(lcfirst(self::FIND_ENTITY_NAME), lcfirst($replacement), $filePath);

        return $this;
    }

    protected function replacePluralEntityName(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(self::FIND_ENTITY_NAME_PLURAL, $replacement, $filePath);
        $this->findReplace(lcfirst(self::FIND_ENTITY_NAME_PLURAL), lcfirst($replacement), $filePath);

        return $this;
    }

    protected function replaceNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(self::FIND_NAMESPACE, $replacement, $filePath);

        return $this;
    }

    protected function replaceInPath(string $find, string $replace, string $path): AbstractGenerator
    {
        $basename = basename($path);
        $newBasename = str_replace($find, $replace, $basename);
        $moveTo = dirname($path) . '/' . $newBasename;
        $this->getFilesystem()->rename($path, $moveTo);

        return $this;
    }

    protected function requireEntityFromFqnAndGetSubDirectories(string $fullyQualifiedName): array
    {
        list($className, , $subDirectories) = $this->parseFullyQualifiedName($fullyQualifiedName);
        $this->requireEntity($className, $subDirectories);
        return $subDirectories;
    }

    protected function getPathForClassOrTrait(string $className, array $subDirectories): string
    {
        $path = realpath($this->pathToProjectSrcRoot) . '/' . implode('/', $subDirectories) . '/' . $className . '.php';
        return $path;
    }


    protected function requireEntity(string $className, array $subDirectories)
    {
        $path = $this->getPathForClassOrTrait($className, $subDirectories);
        require_once($path);
    }
}
