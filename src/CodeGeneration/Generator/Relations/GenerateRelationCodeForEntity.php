<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Relations;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Exception;
use Generator;
use RuntimeException;

use function array_slice;
use function implode;
use function ltrim;
use function realpath;
use function str_replace;
use function ucfirst;

class GenerateRelationCodeForEntity
{
    /**
     * @var NamespaceHelper
     */
    protected NamespaceHelper $namespaceHelper;
    /**
     * @var PathHelper
     */
    protected PathHelper $pathHelper;
    /**
     * @var FindAndReplaceHelper
     */
    protected FindAndReplaceHelper $findAndReplaceHelper;
    /**
     * @var string
     */
    protected string $projectRootNamespace;
    /**
     * @var string
     */
    private string $entityFqn;
    /**
     * @var string
     */
    private string $entityInterfaceFqn;

    /**
     * @var string
     */
    private string $pathToProjectRoot;
    /**
     * @var string
     */
    private string $srcSubFolderName;
    /**
     * @var string
     */
    private string $destinationDirectory;
    /**
     * @var string
     */
    private string $singularNamespace;
    /**
     * @var string
     */
    private string $pluralNamespace;
    /**
     * @var string
     */
    private string $singularNamespacedName;
    /**
     * @var string
     */
    private string $pluralNamespacedName;
    /**
     * @var array
     */
    private array $dirsToRename;
    /**
     * @var array
     */
    private array $filesCreated;


    public function __construct(
        string $entityFqn,
        string $pathToProjectRoot,
        string $projectRootNamespace,
        string $srcSubFolderName,
        NamespaceHelper $namespaceHelper,
        PathHelper $pathHelper,
        FindAndReplaceHelper $findAndReplaceHelper
    ) {
        $this->entityFqn            = $entityFqn;
        $this->pathToProjectRoot    = $pathToProjectRoot;
        $this->projectRootNamespace = $projectRootNamespace;
        $this->srcSubFolderName     = $srcSubFolderName;
        $this->namespaceHelper      = $namespaceHelper;
        $this->pathHelper           = $pathHelper;
        $this->findAndReplaceHelper = $findAndReplaceHelper;
    }

    /**
     * @param Generator $relativePathRelationsGenerator
     *
     * @throws DoctrineStaticMetaException
     */
    public function __invoke(Generator $relativePathRelationsGenerator)
    {
        try {
            $this->initialiseVariables();
            $this->copyRelationsTemplateToDestinationDirectory();
            $this->processPaths($relativePathRelationsGenerator);
            $this->renamePathsForCreatedFiles();
            $this->renameDirectories();
            $this->updateNamespace();
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception generating relation for entity ' . $this->entityFqn . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Initialise the values for all requires variable
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @throws DoctrineStaticMetaException
     */
    private function initialiseVariables(): void
    {
        $this->entityInterfaceFqn = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($this->entityFqn);
        [$className, , $subDirs] = $this->namespaceHelper->parseFullyQualifiedName(
            $this->entityFqn,
            $this->srcSubFolderName,
            $this->projectRootNamespace
        );

        $this->singularNamespacedName = $this->namespaceHelper->getSingularNamespacedName(
            $this->entityFqn,
            $subDirs
        );
        $this->pluralNamespacedName   = $this->namespaceHelper->getPluralNamespacedName(
            $this->entityFqn,
            $subDirs
        );
        $this->setDestinationDirectory(
            $className,
            $subDirs
        );
        $plural                  = ucfirst(MappingHelper::getPluralForFqn($this->entityFqn));
        $singular                = ucfirst(MappingHelper::getSingularForFqn($this->entityFqn));
        $nsNoEntities            = implode('\\', array_slice($subDirs, 2));
        $this->singularNamespace = ltrim($nsNoEntities . '\\' . $singular, '\\');
        $this->pluralNamespace   = ltrim($nsNoEntities . '\\' . $plural, '\\');
        $this->dirsToRename      = [];
        $this->filesCreated      = [];
    }

    /**
     * Calculate and set the destination full path to the destination directory for the generated relations files
     *
     * @param string $className
     * @param array  $subDirsNoEntities
     *
     * @return void
     */
    private function setDestinationDirectory(
        string $className,
        array $subDirsNoEntities
    ): void {

        $subDirsNoEntities = array_slice($subDirsNoEntities, 2);

        $this->destinationDirectory = $this->pathHelper->resolvePath(
            $this->pathToProjectRoot
            . '/' . $this->srcSubFolderName
            . AbstractGenerator::ENTITY_RELATIONS_FOLDER_NAME
            . implode(
                '/',
                $subDirsNoEntities
            )
            . '/' . $className
        );
    }

    /**
     * Copy all the relations code templates into the destination directory read for processing
     *
     * @throws DoctrineStaticMetaException
     */
    private function copyRelationsTemplateToDestinationDirectory(): void
    {
        $this->pathHelper->copyTemplateDirectoryAndGetPath(
            $this->pathToProjectRoot,
            AbstractGenerator::RELATIONS_TEMPLATE_PATH,
            $this->destinationDirectory
        );
    }

    /**
     * Loop through the iterator paths and process each item
     *
     * @param Generator $relativePathRelationsGenerator
     */
    private function processPaths(Generator $relativePathRelationsGenerator): void
    {
        foreach ($relativePathRelationsGenerator as $path => $fileInfo) {
            $fullPath = $this->destinationDirectory . "/$path";
            $path     = realpath($fullPath);
            if (false === $path) {
                throw new RuntimeException("path $fullPath does not exist");
            }
            if ($fileInfo->isDir()) {
                $this->dirsToRename[] = $path;
                continue;
            }
            $this->performFindAndReplaceInFile($path);
            $this->filesCreated[] = $path;
        }
    }

    /**
     * Perform the find and replace operations on the specified file
     *
     * @param string $path
     */
    private function performFindAndReplaceInFile(
        string $path
    ): void {
        $this->findAndReplaceHelper->findReplace(
            'use ' . RelationsGenerator::FIND_ENTITIES_NAMESPACE . '\\' . RelationsGenerator::FIND_ENTITY_NAME,
            "use {$this->entityFqn}",
            $path
        );
        $this->findAndReplaceHelper->findReplace(
            'use ' .
            RelationsGenerator::FIND_ENTITY_INTERFACE_NAMESPACE .
            '\\' .
            RelationsGenerator::FIND_ENTITY_INTERFACE_NAME,
            "use {$this->entityInterfaceFqn}",
            $path
        );
        $this->findAndReplaceHelper->findReplaceRegex(
            '%use(.+?)Relations\\\TemplateEntity(.+?);%',
            'use ${1}Relations\\' . $this->singularNamespace . '${2};',
            $path
        );
        $this->findAndReplaceHelper->findReplaceRegex(
            '%use(.+?)Relations\\\TemplateEntity(.+?);%',
            'use ${1}Relations\\' . $this->pluralNamespace . '${2};',
            $path
        );
        $this->findAndReplaceHelper->findReplaceRegex(
            '%(Has|Reciprocates)(Required|)TemplateEntity%',
            '${1}${2}' . $this->singularNamespacedName,
            $path
        );
        $this->findAndReplaceHelper->findReplaceRegex(
            '%(Has|Reciprocates)(Required|)TemplateEntities%',
            '${1}${2}' . $this->pluralNamespacedName,
            $path
        );
        $this->findAndReplaceHelper->findReplace(
            RelationsGenerator::FIND_ENTITY_INTERFACE_NAME,
            $this->namespaceHelper->basename($this->entityInterfaceFqn),
            $path
        );
        $this->findAndReplaceHelper->replaceName($this->singularNamespacedName, $path);
        $this->findAndReplaceHelper->replacePluralName($this->pluralNamespacedName, $path);
        $this->findAndReplaceHelper->replaceProjectNamespace($this->projectRootNamespace, $path);
    }

    /**
     * Loop through the created files and rename the paths
     *
     * @throws DoctrineStaticMetaException
     */
    private function renamePathsForCreatedFiles(): void
    {
        foreach ($this->filesCreated as $key => $realPath) {
            $this->filesCreated[$key] = $this->pathHelper->renamePathBasenameSingularOrPlural(
                $realPath,
                $this->singularNamespacedName,
                $this->pluralNamespacedName
            );
        }
    }

    /**
     * Loop through all the directories to rename and then rename them
     *
     * @throws DoctrineStaticMetaException
     */
    private function renameDirectories(): void
    {
        //update directory names and update file created paths accordingly
        foreach ($this->dirsToRename as $dirPath) {
            $updateDirPath = $this->pathHelper->renamePathBasenameSingularOrPlural(
                $dirPath,
                $this->singularNamespacedName,
                $this->pluralNamespacedName
            );
            foreach ($this->filesCreated as $k => $filePath) {
                $this->filesCreated[$k] = str_replace($dirPath, $updateDirPath, $filePath);
            }
        }
    }

    /**
     * Update the namespace in all the created files
     *
     * @throws DoctrineStaticMetaException
     */
    private function updateNamespace(): void
    {
        //now path is totally sorted, update namespace based on path
        foreach ($this->filesCreated as $filePath) {
            $this->findAndReplaceHelper->setNamespaceFromPath(
                $filePath,
                $this->srcSubFolderName,
                $this->projectRootNamespace
            );
        }
    }
}
