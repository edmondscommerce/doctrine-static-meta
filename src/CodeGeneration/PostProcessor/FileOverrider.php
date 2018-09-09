<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor;

use EdmondsCommerce\DoctrineStaticMeta\Config;
use Symfony\Component\Filesystem\Filesystem;

class FileOverrider
{
    /**
     * The default path to the overrides folder, relative to the project root
     */
    public const OVERRIDES_PATH = 'build/overrides';

    private const EXTENSION_LENGTH_NO_HASH   = 4;
    private const EXTENSION_LENGTH_WITH_HASH = 37;

    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var string
     */
    private $pathToProjectRoot;

    /**
     * @var string
     */
    private $pathToOverridesDirectory;

    public function __construct(Filesystem $filesystem, Config $config)
    {
        $this->setPathToProjectRoot($config::getProjectRootDirectory());
        $this->pathToOverridesDirectory = $this->pathToProjectRoot . '/' . self::OVERRIDES_PATH;
        $this->filesystem               = $filesystem;
    }

    /**
     * @param string $pathToProjectRoot
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): AbstractGenerator
    {
        $this->pathToProjectRoot = $this->getRealPath($pathToProjectRoot);

        return $this;
    }

    private function getRealPath(string $path): string
    {
        $realPath = \realpath($path);
        if (false === $realPath) {
            throw new \RuntimeException('Invalid path ' . $path);
        }

        return $realPath;
    }

    /**
     * Create a new Override File by copying the file from the project into the project's overrides directory
     *
     * @param string $pathToFileInProject
     *
     * @return string
     */
    public function createNewOverride(string $pathToFileInProject): string
    {
        if (null !== $this->getOverrideForPath($pathToFileInProject)) {
            throw new \RuntimeException('Override already exists for path ' . $pathToFileInProject);
        }
        $overridePath =
            $this->getOverrideDirectoryForFile($pathToFileInProject) .
            $this->getFileNameNoExtensionForPath($pathToFileInProject) .
            $this->getProjectFileHash($pathToFileInProject) .
            '.php';
        copy($this->pathToProjectRoot . '/' . $pathToFileInProject, $overridePath);

        return $overridePath;
    }

    private function getOverrideForPath(string $pathToFileInProject): ?string
    {
        $fileDirectory       = $this->getOverrideDirectoryForFile($pathToFileInProject);
        $fileNameNoExtension = $this->getFileNameNoExtensionForPath($pathToFileInProject);
        $filesInDirectory    = glob("$fileDirectory/$fileNameNoExtension*");
        if ([] === $filesInDirectory) {
            return null;
        }
        if (1 === count($filesInDirectory)) {
            return $fileDirectory . '/' . current($filesInDirectory);
        }
        throw new \RuntimeException(
            'Found more than one override in path ' . $fileDirectory . ': '
            . print_r($filesInDirectory, true)
        );
    }

    private function getOverrideDirectoryForFile(string $pathToFileInProject): string
    {
        return $this->getRealPath($this->getPathToOverridesDirectory() . \dirname($pathToFileInProject));
    }

    /**
     * @return string
     */
    public function getPathToOverridesDirectory(): string
    {
        return $this->getRealPath($this->pathToOverridesDirectory);
    }

    /**
     * @param string $pathToOverridesDirectory
     *
     * @return FileOverrider
     */
    public function setPathToOverridesDirectory(string $pathToOverridesDirectory): FileOverrider
    {
        $this->pathToOverridesDirectory = $this->getRealPath($pathToOverridesDirectory);

        return $this;
    }

    private function getFileNameNoExtensionForPath(string $pathToFileInProject): string
    {
        $fileName = basename($pathToFileInProject);

        return substr($fileName, 0, -4);
    }

    private function getProjectFileHash(string $pathToFileInProject): string
    {
        $contents = \ts\file_get_contents($this->pathToProjectRoot . '/' . $pathToFileInProject);

        return md5($contents);
    }

    /**
     * Loop over all the override files and update with the file contents from the project
     */
    public function updateOverrideFiles(): void
    {
        foreach ($this->getOverridesIterator() as $fileInfo) {
            $pathToFileInOverrides = $fileInfo->getFilename();
            $pathToFileInProject   = $this->getProjectFilePathFromOverridePath();
            copy($pathToFileInProject, $pathToFileInOverrides);
        }
    }

    /**
     * @return \Generator|\SplFileInfo[]
     */
    private function getOverridesIterator(): \Generator
    {
        try {
            $recursiveIterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $this->getPathToOverridesDirectory(),
                    \RecursiveDirectoryIterator::SKIP_DOTS
                ),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($recursiveIterator as $path => $fileInfo) {
                yield $fileInfo;
            }
        } finally {
            $recursiveIterator = null;
            unset($recursiveIterator);
        }
    }

    private function getProjectFilePathFromOverridePath(string $pathToFileInOverrides): string
    {
        $relativePath = substr($pathToFileInOverrides, strlen($this->getPathToOverridesDirectory()));
        $relativeDir  = dirname($relativePath);
        $filename     = basename($pathToFileInOverrides);
        $filename     = substr($filename, 0, -37) . '.php';

        return $this->getRealPath($this->pathToProjectRoot . '/' . $relativeDir . '/' . $filename);
    }

    public function applyOverrides(): void
    {
        $errors = [];
        foreach ($this->getOverridesIterator() as $fileInfo) {
            $pathToFileInOverrides = $fileInfo->getFilename();
            $pathToFileInProject   = $this->getProjectFilePathFromOverridePath($pathToFileInOverrides);
            if ($this->overrideFileHashIsCorrect($$pathToFileInOverrides)) {
                copy($pathToFileInOverrides, $pathToFileInProject);
                continue;
            }
            $errors[$pathToFileInOverrides] = $this->getProjectFileHash($pathToFileInProject);
        }
        if ([] !== $errors) {
            throw new \RuntimeException('These file hashes were not up to date:' . print_r($errors, true));
        }
    }

    private function overrideFileHashIsCorrect(string $pathToFileInOverrides): bool
    {
        $filenameParts = explode('.', basename($pathToFileInOverrides));
        if (3 !== count($filenameParts)) {
            throw new \RuntimeException('Invalid override filename ' . $pathToFileInOverrides);
        }
        $hash                = $filenameParts[1];
        $pathToFileInProject = $this->getProjectFilePathFromOverridePath($pathToFileInOverrides);

        return $hash === $this->getProjectFileHash($pathToFileInProject);
    }
}
