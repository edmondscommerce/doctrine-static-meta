<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor;

class CopyPhpstormMeta
{
    /**
     * @var string
     */
    private $pathToProjectRoot;


    public function run(): void
    {
        $targetDir   = $this->getTargetDir();
        $sourceFiles = $this->getSourceFiles();
        foreach ($sourceFiles as $sourceFilePath) {
            $sourceFilename = basename($sourceFilePath);
            $targetFilePath = $targetDir . '/' . $sourceFilename;
            if (file_exists($targetFilePath)) {
                unlink($targetFilePath);
            }
            copy($sourceFilePath, $targetFilePath);
        }
    }

    private function getTargetDir()
    {
        if (null === $this->pathToProjectRoot) {
            throw new \RuntimeException('You must set the project root path before running this process');
        }
        $targetDir = $this->pathToProjectRoot . '/.phpstorm.meta.php';
        if (!is_dir($targetDir) && mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
            throw new \RuntimeException('Failed making targetDir ' . $targetDir);
        }

        return realpath($targetDir);
    }

    private function getSourceFiles(): array
    {
        return glob(__DIR__ . '/../../../.phpstorm.meta.php/*.php');
    }

    /**
     * @param string $pathToProjectRoot
     *
     * @return self
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): self
    {
        $this->pathToProjectRoot = $pathToProjectRoot;

        return $this;
    }
}
