<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

class ReplaceProjectRootNamespaceProcess implements ProcessInterface
{
    public const FIND_NAMESPACE = 'TemplateNamespace\\';
    /**
     * @var string
     */
    protected $projectRootNamespace;

    public function run(File\FindReplace $findReplace): void
    {
        if (null === $this->projectRootNamespace) {
            throw new \RuntimeException('You must set the project root namespace in ' . __CLASS__);
        }
        $findReplace->findReplace(self::FIND_NAMESPACE, $this->projectRootNamespace);
    }

    /**
     * @param string $projectRootNamespace
     *
     * @return ReplaceProjectRootNamespaceProcess
     */
    public function setProjectRootNamespace(string $projectRootNamespace): ReplaceProjectRootNamespaceProcess
    {
        $this->projectRootNamespace = rtrim($projectRootNamespace, '\\') . '\\';

        return $this;
    }
}
