<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use Symfony\Component\Filesystem\Filesystem;

class CodeCopier
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var FindAndReplaceHelper
     */
    private $findAndReplaceHelper;

    public function __construct(Filesystem $filesystem, FindAndReplaceHelper $findAndReplaceHelper)
    {
        $this->filesystem = $filesystem;
        $this->findAndReplaceHelper = $findAndReplaceHelper;
    }

    public function copy(
        string $srcDir,
        string $destinationPath,
        string $findNamespace,
        string $replaceNamespace
    ): void {
        $this->filesystem->mirror($srcDir, $destinationPath);
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($destinationPath));

        foreach ($iterator as $info) {
            /**
             * @var \SplFileInfo $info
             */
            if (false === $info->isFile()) {
                continue;
            }
            $contents = file_get_contents($info->getPathname());

            $updated = \preg_replace(
                '%' . $this->findAndReplaceHelper->escapeSlashesForRegex('(\\|)' . $findNamespace . '\\') . '%',
                '$1' . $replaceNamespace . '\\',
                $contents
            );
            $updated = \preg_replace(
                '%' .
                $this->findAndReplaceHelper->escapeSlashesForRegex(
                    '(\\|)'
                    . str_replace('\\', '\\\\', $findNamespace) .
                    '\\'
                ) . '%',
                '$1' . str_replace('\\', '\\\\', $replaceNamespace) . '\\',
                $updated
            );
            file_put_contents($info->getPathname(), $updated);
        }
    }
}
