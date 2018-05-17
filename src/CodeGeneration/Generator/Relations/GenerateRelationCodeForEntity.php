<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Relations;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class GenerateRelationCodeForEntity
{
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;
    /**
     * @var \Generator
     */
    protected $relativePathRelationsGenerator;
    /**
     * @var PathHelper
     */
    protected $pathHelper;
    /**
     * @var FindAndReplaceHelper
     */
    protected $findAndReplaceHelper;
    /**
     * @var string
     */
    protected $projectRootNamespace;
    /**
     * @var string
     */
    private $entityFqn;
    /**
     * @var string
     */
    private $pathToProjectRoot;
    /**
     * @var string
     */
    private $srcSubFolderName;
    /**
     * @var CodeHelper
     */
    private $codeHelper;

    /**
     * @var string
     */
    private $destinationDirectory;

    /**
     * @var string
     */
    private $plural;

    /**
     * @var string
     */
    private $singular;

    /**
     * @var string
     */
    private $nsNoEntities;

    /**
     * @var string
     */
    private $singularWithNs;

    /**
     * @var string
     */
    private $pluralWithNs;

    /**
     * @var string
     */
    private $singularNamespacedName;

    /**
     * @var string
     */
    private $pluralNamespacedName;

    /**
     * @var array
     */
    private $dirsToRename;

    /**
     * @var array
     */
    private $filesCreated;


    public function __construct(
        string $entityFqn,
        string $pathToProjectRoot,
        string $projectRootNamespace,
        string $srcSubFolderName,
        CodeHelper $codeHelper,
        NamespaceHelper $namespaceHelper,
        \Generator $relativePathRelationsGenerator,
        PathHelper $pathHelper,
        FindAndReplaceHelper $findAndReplaceHelper
    ) {
        $this->entityFqn                      = $entityFqn;
        $this->pathToProjectRoot              = $pathToProjectRoot;
        $this->projectRootNamespace           = $projectRootNamespace;
        $this->srcSubFolderName               = $srcSubFolderName;
        $this->codeHelper                     = $codeHelper;
        $this->namespaceHelper                = $namespaceHelper;
        $this->relativePathRelationsGenerator = $relativePathRelationsGenerator;
        $this->pathHelper                     = $pathHelper;
        $this->findAndReplaceHelper           = $findAndReplaceHelper;
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

        $subDirsNoEntities = \array_slice($subDirsNoEntities, 2);

        $this->destinationDirectory = $this->codeHelper->resolvePath(
            $this->pathToProjectRoot
            .'/'.$this->srcSubFolderName
            .AbstractGenerator::ENTITY_RELATIONS_FOLDER_NAME
            .\implode(
                '/',
                $subDirsNoEntities
            )
            .'/'.$className
        );
    }

    /**
     * Copy all the relations code templates into the destination directory read for processing
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
     * Perform the find and replace operations on the specified file
     *
     * @param string $path
     */
    private function performFindAndReplaceInFile(
        string $path
    ): void {
        $this->findAndReplaceHelper->findReplace(
            'use '.RelationsGenerator::FIND_ENTITIES_NAMESPACE.'\\'.RelationsGenerator::FIND_ENTITY_NAME,
            "use {$this->entityFqn}",
            $path
        );
        $this->findAndReplaceHelper->findReplaceRegex(
            '%use(.+?)Relations\\\TemplateEntity(.+?);%',
            'use ${1}Relations\\'.$this->singularWithNs.'${2};',
            $path
        );
        $this->findAndReplaceHelper->findReplaceRegex(
            '%use(.+?)Relations\\\TemplateEntity(.+?);%',
            'use ${1}Relations\\'.$this->pluralWithNs.'${2};',
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
     * Initialise the values for all requires variable
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @throws DoctrineStaticMetaException
     */
    private function initialiseVariables(): void
    {
        list($className, , $subDirs) = $this->namespaceHelper->parseFullyQualifiedName(
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
        $this->plural         = \ucfirst(MappingHelper::getPluralForFqn($this->entityFqn));
        $this->singular       = \ucfirst(MappingHelper::getSingularForFqn($this->entityFqn));
        $this->nsNoEntities   = \implode('\\', \array_slice($subDirs, 2));
        $this->singularWithNs = \ltrim($this->nsNoEntities.'\\'.$this->singular, '\\');
        $this->pluralWithNs   = \ltrim($this->nsNoEntities.'\\'.$this->plural, '\\');
        $this->dirsToRename   = [];
        $this->filesCreated   = [];
    }

    /**
     * Loop through the iterator paths and process each item
     */
    private function processPaths(): void
    {
        foreach ($this->relativePathRelationsGenerator as $path => $fileInfo) {
            $fullPath = $this->destinationDirectory."/$path";
            $path     = \realpath($fullPath);
            if (false === $path) {
                throw new \RuntimeException("path $fullPath does not exist");
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
                $this->filesCreated[$k] = \str_replace($dirPath, $updateDirPath, $filePath);
            }
        }
    }

    /**
     * Update the namespace in all the created files
     *
     * @throws DoctrineStaticMetaException
     */
    private function updateNamespace()
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

    /**
     * @throws DoctrineStaticMetaException
     */
    public function __invoke()
    {
        try {
            $this->initialiseVariables();
            $this->copyRelationsTemplateToDestinationDirectory();
            $this->processPaths();
            $this->renamePathsForCreatedFiles();
            $this->renameDirectories();
            $this->updateNamespace();
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception generating relation for entity '.$this->entityFqn.': '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
