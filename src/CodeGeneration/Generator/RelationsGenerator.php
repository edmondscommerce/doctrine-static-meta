<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use SplFileInfo;

class RelationsGenerator extends AbstractGenerator
{
    public function generateRelationsForEntity(string $fullyQualifiedName)
    {

        list($className, $namespace, $subDirectories) = $this->parseFQN($fullyQualifiedName);
        $this->requireEntity($className, $subDirectories);
        $singular                      = ucfirst($fullyQualifiedName::getSingular());
        $plural                        = ucfirst($fullyQualifiedName::getPlural());
        $subDirectoriesWithoutEntities = $subDirectories;
        array_shift($subDirectoriesWithoutEntities);
        $destinationDirectory = $this->pathToProjectRoot.'/'.$this->entitiesFolderName.'/Traits/Relations/'.implode(
                '/',
                $subDirectoriesWithoutEntities
            ).'/'.$className;
        $this->copyTemplateDirectoryAndGetPath(
            self::RELATIONS_TEMPLATE_PATH,
            $destinationDirectory
        );
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                realpath($destinationDirectory),
                \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        /**
         * @var SplFileInfo[] $iterator
         */
        $dirsToRename = [];
        foreach ($iterator as $path => $i) {
            if (!$i->isDir()) {
                $this->replaceEntityName($singular, $path);
                $this->replacePluralEntityName($plural, $path);
                $this->replaceNamespace($namespace, $path);
                $this->renamePathSingularOrPlural($path, $singular, $plural);
            } else {
                $dirsToRename[] = $path;
            }
        }
        foreach ($dirsToRename as $path) {
            $this->renamePathSingularOrPlural($path, $singular, $plural);
        }
    }

    protected function renamePathSingularOrPlural(string $path, string $singular, string $plural): AbstractGenerator
    {
        $find     = self::FIND_ENTITY_NAME;
        $replace  = $singular;
        $basename = basename($path);
        if (false !== strpos($basename, self::FIND_ENTITY_NAME_PLURAL)) {
            $find    = self::FIND_ENTITY_NAME_PLURAL;
            $replace = $plural;
        }
        $this->replaceInPath($find, $replace, $path);

        return $this;
    }
}
