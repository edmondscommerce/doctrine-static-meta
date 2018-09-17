<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

class ReplaceEntitiesNamespaceProcess implements ProcessInterface
{
    /**
     * @var string
     */
    private $entitySubNamespace;

    public function setEntitySubNamespace(string $entitySubNamespace)
    {
        $entitySubNamespace = '\\' . trim($entitySubNamespace, '\\');
        if (0 === strpos($entitySubNamespace, '\\Entities\\')) {
            throw new \RuntimeException(
                'You must not include the Entities bit at the start, its just the sub namespace'
            );
        }
        $this->entitySubNamespace = $entitySubNamespace;

        return $this;
    }

    public function run(File\FindReplace $findReplace): void
    {
        $this->replaceEntities($findReplace);
        $this->replaceEntity($findReplace);
    }

    private function replaceEntities(File\FindReplace $findReplace): void
    {
        $pattern     = $findReplace->escapeSlashesForRegex('%\\Entities(\\|;)%');
        $replacement = '\\Entities' . $this->entitySubNamespace . '$1';
        $findReplace->findReplaceRegex($pattern, $replacement);
    }

    private function replaceEntity(File\FindReplace $findReplace)
    {
        $pattern     = $findReplace->escapeSlashesForRegex('%\\Entity\\([^\\]+?)(\\|;)%');
        $replacement = '\\Entity\\\$1' . $this->entitySubNamespace . '$2';
        $findReplace->findReplaceRegex($pattern, $replacement);
    }
}