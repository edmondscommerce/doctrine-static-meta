<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation;

interface CreatorInterface
{

    public const SRC_FOLDER = 'src';

    public const TESTS_FOLDER = 'tests';

    /**
     * Create the new Object and return the File object for the target file with:
     * - correct path to write to
     * - fully updated contents
     * ready to have `putContents()` called
     *
     * @param string $newObjectFqn
     *
     * @return $this
     */
    public function createTargetFileObject(string $newObjectFqn);

    /**
     * Write the created file object to the filesystem and return the path to which is was written
     *
     * @return string
     */
    public function write(): string;
}