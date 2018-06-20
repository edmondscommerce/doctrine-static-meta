<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use Symfony\Component\Filesystem\Filesystem;

class ArchetypeEmbeddableGenerator
{
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;
    /**
     * @var CodeHelper
     */
    protected $codeHelper;

    public function __construct(
        Filesystem $filesystem,
        NamespaceHelper $namespaceHelper,
        CodeHelper $codeHelper
    ) {
        $this->filesystem      = $filesystem;
        $this->namespaceHelper = $namespaceHelper;
        $this->codeHelper      = $codeHelper;
    }

    /**
     * Create a new Embeddable Object plus associated traits and interfaces by copying and modifying a standard library
     * Embeddable
     *
     * @param string $embeddableObjectFqn
     * @param string $archetypeEmbeddableObjectFqn
     */
    public function createFromArchetype(string $embeddableObjectFqn, string $archetypeEmbeddableObjectFqn)
    {

    }

    private function copyObject()
    {
        $this->filesystem->
    }
}
