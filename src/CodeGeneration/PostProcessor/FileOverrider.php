<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor;

/**
 * This class provides the necessary functoinality to allow you to maintain a set of file overrides and to safely apply
 * them as part of a post process to your main build process
 */
class FileOverrider
{
    /**
     * The default path to the overrides folder, relative to the project root
     */
    public const OVERRIDES_PATH = '/build/overrides';

    private const EXTENSION_LENGTH_NO_HASH   = 4;
    private const EXTENSION_LENGTH_WITH_HASH = 37;

    /**
     * @var string
     */
    private $pathToProjectRoot;

    /**
     * @var string
     */
    private $pathToOverridesDirectory;

    public function __construct(
        string $pathToProjectRoot = null,
        string $relativePathToOverridesDirectory = self::OVERRIDES_PATH
    ) {
        if (null !== $pathToProjectRoot) {
            $this->setPathToProjectRoot($pathToProjectRoot);
            $this->setPathToOverridesDirectory($this->pathToProjectRoot . '/' . $relativePathToOverridesDirectory);
        }
    }

    /**
     * @param string $pathToProjectRoot
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): self
    {
        $this->pathToProjectRoot = $this->getRealPath($pathToProjectRoot);
        $this->setPathToOverridesDirectory($this->pathToProjectRoot . self::OVERRIDES_PATH);

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
        $relativePathToFileInProject = $this->getRelativePathToFile($pathToFileInProject);
        if (null !== $this->getOverrideForPath($relativePathToFileInProject)) {
            throw new \RuntimeException('Override already exists for path ' . $relativePathToFileInProject);
        }
        $overridePath =
            $this->getOverrideDirectoryForFile($relativePathToFileInProject) .
            '/' . $this->getFileNameNoExtensionForPath($relativePathToFileInProject) .
            '.' . $this->getProjectFileHash($relativePathToFileInProject) .
            '.php';
        copy($this->pathToProjectRoot . '/' . $relativePathToFileInProject, $overridePath);

        return $this->getRelativePathToFile($overridePath);
    }

    private function getRelativePathToFile(string $pathToFileInProject): string
    {
        return str_replace($this->pathToProjectRoot, '', $this->getRealPath($pathToFileInProject));
    }

    private function getOverrideForPath(string $relativePathToFileInProject): ?string
    {
        $fileDirectory       = $this->getOverrideDirectoryForFile($relativePathToFileInProject);
        $fileNameNoExtension = $this->getFileNameNoExtensionForPath($relativePathToFileInProject);
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

    private function getOverrideDirectoryForFile(string $relativePathToFileInProject): string
    {
        $path = $this->getPathToOverridesDirectory() . \dirname($relativePathToFileInProject);
        if (!is_dir($path) && !(mkdir($path, 0777, true) && is_dir($path))) {
            throw new \RuntimeException('Failed making override directory path ' . $path);
        }

        return $this->getRealPath($path);
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

    private function getFileNameNoExtensionForPath(string $relativePathToFileInProject): string
    {
        $fileName = basename($relativePathToFileInProject);

        return substr($fileName, 0, -self::EXTENSION_LENGTH_NO_HASH);
    }

    private function getProjectFileHash(string $relativePathToFileInProject): string
    {
        $contents = \ts\file_get_contents($this->pathToProjectRoot . '/' . $relativePathToFileInProject);

        return md5($contents);
    }

    /**
     * Loop over all the override files and update with the file contents from the project
     *
     * @return array|string[] the file paths that have been updated
     */
    public function updateOverrideFiles(): array
    {
        $filesUpdated = [];
        foreach ($this->getOverridesIterator() as $pathToFileInOverrides) {
            $relativePathToFileInProject = $this->getRelativePathFromOverridePath($pathToFileInOverrides);
            copy($this->pathToProjectRoot . $relativePathToFileInProject, $pathToFileInOverrides);
            $filesUpdated[] = $this->getRelativePathFromOverridePath($pathToFileInOverrides);
        }

        return $this->sortFiles($filesUpdated);
    }

    /**
     * Yield file paths in the override folder
     *
     * @return \Generator|string[]
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
            foreach ($recursiveIterator as $fileInfo) {
                /**
                 * @var \SplFileInfo $fileInfo
                 */
                if ($fileInfo->isFile()) {
                    yield $fileInfo->getPathname();
                }
            }
        } finally {
            $recursiveIterator = null;
            unset($recursiveIterator);
        }
    }

    private function getRelativePathFromOverridePath(string $pathToFileInOverrides): string
    {
        $relativePath = substr($pathToFileInOverrides, strlen($this->getPathToOverridesDirectory()));
        $relativeDir  = dirname($relativePath);
        $filename     = basename($pathToFileInOverrides);
        $filename     = substr($filename, 0, -self::EXTENSION_LENGTH_WITH_HASH) . '.php';

        return $this->getRelativePathToFile(
            $this->getRealPath($this->pathToProjectRoot . '/' . $relativeDir . '/' . $filename)
        );
    }

    private function sortFiles(array $files): array
    {
        sort($files, SORT_STRING);

        return $files;
    }

    /**
     * Loop over all the override files and copy into the project
     *
     * @return array|string[] the file paths that have been updated
     */
    public function applyOverrides(): array
    {
        $filesUpdated = [];
        $errors       = [];
        foreach ($this->getOverridesIterator() as $pathToFileInOverrides) {
            $relativePathToFileInProject = $this->getRelativePathFromOverridePath($pathToFileInOverrides);
            if ($this->overrideFileHashIsCorrect($pathToFileInOverrides)) {
                copy($pathToFileInOverrides, $this->pathToProjectRoot . $relativePathToFileInProject);
                $filesUpdated[] = $relativePathToFileInProject;
                continue;
            }
            $errors[$pathToFileInOverrides] = $this->getProjectFileHash($relativePathToFileInProject);
        }
        if ([] !== $errors) {
            throw new \RuntimeException('These file hashes were not up to date:' . print_r($errors, true));
        }

        return $this->sortFiles($filesUpdated);
    }

    private function overrideFileHashIsCorrect(string $pathToFileInOverrides): bool
    {
        $filenameParts = explode('.', basename($pathToFileInOverrides));
        if (3 !== count($filenameParts)) {
            throw new \RuntimeException('Invalid override filename ' . $pathToFileInOverrides);
        }
        $hash                        = $filenameParts[1];
        $relativePathToFileInProject = $this->getRelativePathFromOverridePath($pathToFileInOverrides);

        return $hash === $this->getProjectFileHash($relativePathToFileInProject);
    }
}
