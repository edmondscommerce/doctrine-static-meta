<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\Config;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_keys;
use function file_exists;
use function file_get_contents;
use function is_dir;
use function preg_match;
use function str_replace;
use function strlen;

/**
 * Class UnusedCodeRemover
 *
 * Finds and removes generated code that does not seem to be used
 *
 * Caution - this is destructive - do not use in a dirty repo!!!
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration
 */
class UnusedRelationsRemover
{
    /**
     * @var NamespaceHelper
     */
    protected NamespaceHelper $namespaceHelper;
    /**
     * @var Config
     */
    protected Config $config;
    /**
     * @var array
     */
    private array $filesRemoved = [];
    /**
     * @var string
     */
    private string $pathToProjectRoot;

    /**
     * @param string $pathToProjectRoot
     *
     * @return UnusedRelationsRemover
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): UnusedRelationsRemover
    {
        $this->pathToProjectRoot = $pathToProjectRoot;

        return $this;
    }

    /**
     * @param string $projectRootNamespace
     *
     * @return UnusedRelationsRemover
     */
    public function setProjectRootNamespace(string $projectRootNamespace): UnusedRelationsRemover
    {
        $this->projectRootNamespace = $projectRootNamespace;

        return $this;
    }
    /**
     * @var string
     */
    private string $projectRootNamespace;
    /**
     * @var array
     */
    private array $relationTraits = [];
    /**
     * @var array
     */
    private array $entitySubFqnsToName = [];
    /**
     * @var array
     */
    private array $entityPaths = [];

    public function __construct(NamespaceHelper $namespaceHelper, Config $config)
    {
        $this->namespaceHelper      = $namespaceHelper;
        $this->config               = $config;
        $this->pathToProjectRoot    = $this->config::getProjectRootDirectory();
        $this->projectRootNamespace = $this->namespaceHelper->getProjectRootNamespaceFromComposerJson();
    }


    public function run(): array
    {
        $this->initArrayOfRelationTraits();
        $this->initAllEntitySubFqns();
        foreach (array_keys($this->entitySubFqnsToName) as $entitySubFqn) {
            $this->removeUnusedEntityRelations($entitySubFqn);
        }

        return $this->filesRemoved;
    }

    private function initArrayOfRelationTraits(): void
    {
        $this->relationTraits = [];
        $pluralRelations      = $this->getFileInfoObjectsInDirs(
            [
                __DIR__ . '/../../codeTemplates/src/Entity/Relations/TemplateEntity/Traits/HasTemplateEntities',
                __DIR__ . '/../../codeTemplates/src/Entity/Relations/TemplateEntity/Traits/HasRequiredTemplateEntities',
            ]
        );
        foreach ($pluralRelations as $pluralRelation) {
            $realPath                                  = $pluralRelation->getRealPath();
            $this->relationTraits['plural'][$realPath] = $this->convertPathToNamespace(
                $this->getSubPathFromSrcAndTrimExtension(
                    $realPath
                )
            );
        }
        $singularRelations = $this->getFileInfoObjectsInDirs(
            [
                __DIR__ . '/../../codeTemplates/src/Entity/Relations/TemplateEntity/Traits/HasTemplateEntity',
                __DIR__ . '/../../codeTemplates/src/Entity/Relations/TemplateEntity/Traits/HasRequiredTemplateEntity',
            ]
        );
        foreach ($singularRelations as $singularRelation) {
            $realPath                                    = $singularRelation->getRealPath();
            $this->relationTraits['singular'][$realPath] = $this->convertPathToNamespace(
                $this->getSubPathFromSrcAndTrimExtension(
                    $realPath
                )
            );
        }
    }

    /**
     * @param array $dirs
     *
     * @return array|SplFileInfo[]
     */
    private function getFileInfoObjectsInDirs(array $dirs): array
    {
        $finder   = new Finder();
        $iterable = $finder->files()->in($dirs);

        return iterator_to_array($iterable);
    }

    private function convertPathToNamespace(string $path): string
    {
        return str_replace('/', '\\', $path);
    }

    private function getSubPathFromSrcAndTrimExtension(string $path): string
    {
        $subPath = substr($path, strpos($path, 'src') + 3);
        $subPath = substr($subPath, 0, strpos($subPath, '.php'));

        return $subPath;
    }

    private function initAllEntitySubFqns(): void
    {
        $files                     = $this->getFileInfoObjectsInDirs([$this->pathToProjectRoot . '/src/Entities']);
        $this->entitySubFqnsToName = [];
        foreach ($files as $file) {
            $realPath                                 = $file->getRealPath();
            $this->entityPaths[$realPath]             = file_get_contents($realPath);
            $entitySubFqn                             = $this->getEntitySubFqnFromEntityFilePath($realPath);
            $this->entitySubFqnsToName[$entitySubFqn] = $this->getPluralSingularFromEntitySubFqn($entitySubFqn);
        }
    }

    private function getEntitySubFqnFromEntityFilePath(string $path): string
    {
        $subPath = $this->getSubPathFromSrcAndTrimExtension($path);

        return str_replace('/', '\\', $subPath);
    }

    private function getPluralSingularFromEntitySubFqn(string $entitySubFqn): array
    {
        $entityFqn = $this->projectRootNamespace . $entitySubFqn;

        return [
            'singular' => ucfirst($entityFqn::getDoctrineStaticMeta()->getSingular()),
            'plural'   => ucfirst($entityFqn::getDoctrineStaticMeta()->getPlural()),
        ];
    }

    private function removeUnusedEntityRelations(string $entitySubFqn): void
    {
        $entitySubSubFqn = $this->getEntitySubSubFqn($entitySubFqn);
        $hasPlural       = $this->removeRelationsBySingularOrPlural('plural', $entitySubSubFqn);
        $hasSingular     = $this->removeRelationsBySingularOrPlural('singular', $entitySubSubFqn);

        if (false === $hasPlural && false === $hasSingular) {
            $this->removeAllRelationFilesForEntity($entitySubSubFqn);

            return;
        }
        if (false === $hasPlural) {
            $this->removeHasPluralOrSingularInterfaceAndAbstract(
                'plural',
                $entitySubFqn,
                $entitySubSubFqn
            );
        }

        if (false === $hasSingular) {
            $this->removeHasPluralOrSingularInterfaceAndAbstract(
                'singular',
                $entitySubFqn,
                $entitySubSubFqn
            );
        }
    }

    private function getEntitySubSubFqn(string $entitySubFqn): string
    {
        return substr($entitySubFqn, strlen('\\Entities\\'));
    }

    private function removeRelationsBySingularOrPlural(string $singularOrPlural, string $entitySubSubFqn): bool
    {
        $foundUsedRelations = false;

        foreach ($this->relationTraits[$singularOrPlural] as $relationTraitSubFqn) {
            $relationRegex = $this->getRelationRegexPatternFromRelationTraitSubFqn($relationTraitSubFqn);
            $pattern       = $this->getRegexForRelationTraitUseStatement($entitySubSubFqn, $relationRegex);
            foreach ($this->entityPaths as $entityFileContents) {
                if (1 === preg_match($pattern, $entityFileContents)) {
                    $foundUsedRelations = true;
                    continue 2;
                }
            }
            $this->removeRelation($entitySubSubFqn, $relationRegex);
        }

        return $foundUsedRelations;
    }

    private function getRelationRegexPatternFromRelationTraitSubFqn(string $relationTraitSubFqn): string
    {

        $baseTraitNamespace = $this->namespaceHelper->basename($relationTraitSubFqn);

        return preg_replace('%TemplateEntit(y|ies)%', '.+?', $baseTraitNamespace);
    }

    private function getRegexForRelationTraitUseStatement(string $entitySubSubFqn, string $relationRegex): string
    {
        $entitySubSubFqn = str_replace('\\', '\\\\', $entitySubSubFqn);

        return <<<REGEXP
%use .+?\\\\Entity\\\\Relations\\\\$entitySubSubFqn([^;]+?)$relationRegex%
REGEXP;
    }

    private function removeRelation(string $entitySubSubFqn, string $relationRegex): void
    {
        $directory = $this->getPathToRelationRootForEntity($entitySubSubFqn);
        if (!is_dir($directory)) {
            return;
        }
        $finder = (new Finder())->files()
                                ->in($directory)
                                ->path('%^(Interfaces|Traits).+?' . $relationRegex . '%');
        $this->removeFoundFiles($finder);
    }

    private function getPathToRelationRootForEntity(string $entitySubSubFqn): string
    {
        return $this->pathToProjectRoot
               . '/src/Entity/Relations/'
               . str_replace(
                   '\\',
                   '/',
                   $entitySubSubFqn
               );
    }

    private function removeFoundFiles(Finder $finder): void
    {
        foreach ($finder as $fileInfo) {
            $this->removeFile($fileInfo->getRealPath());
        }
    }

    private function removeFile(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }
        $this->filesRemoved[] = $path;
        unlink($path);
    }

    private function removeAllRelationFilesForEntity(string $entitySubSubFqn): void
    {
        $relationsPath = $this->getPathToRelationRootForEntity($entitySubSubFqn);
        $directories   = [
            "$relationsPath/Traits",
            "$relationsPath/Interfaces",
        ];
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }
            $finder = (new Finder())->files()
                                    ->in($directory);
            $this->removeFoundFiles($finder);
        }
    }

    private function removeHasPluralOrSingularInterfaceAndAbstract(
        string $pluralOrSingular,
        string $entitySubFqn,
        string $entitySubSubFqn
    ): void {
        $directory = $this->getPathToRelationRootForEntity($entitySubSubFqn);
        if (!is_dir($directory)) {
            return;
        }
        $hasName = $this->entitySubFqnsToName[$entitySubFqn][$pluralOrSingular];
        $finder  = (new Finder())->files()
                                 ->in($directory)
                                 ->path(
                                     '%^(Interfaces|Traits).+?Has' . $hasName . '(/|Abstract\.php|Interface\.php)%'
                                 );
        $this->removeFoundFiles($finder);
    }
}
