<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor;

use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder;
use SplFileInfo;

use function copy;
use function dirname;
use function realpath;

/**
 * This class provides the necessary functionality to allow you to maintain a set of file overrides and to safely apply
 * them as part of a post process to your main build process
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class FileOverrider
{
    /**
     * The default path to the overrides folder, relative to the project root
     */
    public const OVERRIDES_PATH = '/build/overrides';

    private const EXTENSION_LENGTH_NO_HASH_IN_PROJECT     = 4;
    private const EXTENSION_LENGTH_WITH_HASH_IN_OVERRIDES = 46;
    private const OVERRIDE_EXTENSION                      = 'override';

    /**
     * @var string
     */
    private $pathToProjectRoot;
    /**
     * @var string
     */
    private $pathToOverridesDirectory;
    /**
     * @var Differ
     */
    private $differ;

    public function __construct(
        string $pathToProjectRoot = null
    ) {
        if (null !== $pathToProjectRoot) {
            $this->setPathToProjectRoot($pathToProjectRoot);
        }
        $builder = new DiffOnlyOutputBuilder('');
        $this->differ = new Differ($builder);
    }

    /**
     * @return string
     */
    public function getPathToProjectRoot(): string
    {
        return $this->pathToProjectRoot;
    }

    /**
     * @param string $pathToProjectRoot
     *
     * @return $this
     * @throws RuntimeException
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): self
    {
        $this->pathToProjectRoot = $this->getRealPath($pathToProjectRoot);
        $this->setPathToOverridesDirectory($this->pathToProjectRoot . self::OVERRIDES_PATH);

        return $this;
    }

    public function recreateOverride(string $relativePathToFileInOverrides): array
    {
        $overridePath = $this->cleanPath($this->pathToProjectRoot . '/' . $relativePathToFileInOverrides);

        $relativePathToFileInProject = $this->getRelativePathInProjectFromOverridePath($overridePath);

        $old = $relativePathToFileInOverrides . '-old';
        rename($overridePath, $overridePath . '-old');

        $new = $this->createNewOverride($this->pathToProjectRoot . '/' . $relativePathToFileInProject);

        return [$old, $new];
    }

    private function cleanPath(string $path): string
    {
        return preg_replace('%/{2,}%', '/', $path);
    }

    private function getRelativePathInProjectFromOverridePath(string $pathToFileInOverrides): string
    {
        $pathToFileInOverrides = $this->cleanPath($pathToFileInOverrides);
        $relativePath          = substr($pathToFileInOverrides, strlen($this->getPathToOverridesDirectory()));
        $relativeDir           = dirname($relativePath);
        $filename              = basename($pathToFileInOverrides);
        $filename              = substr($filename, 0, -self::EXTENSION_LENGTH_WITH_HASH_IN_OVERRIDES) . '.php';

        return $this->getRelativePathToFile(
            $this->getRealPath($this->pathToProjectRoot . '/' . $relativeDir . '/' . $filename)
        );
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

    private function getRealPath(string $path): string
    {
        $realPath = realpath($path);
        if (false === $realPath) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
            $realPath = realpath($path);
        }

        return $realPath;
    }

    private function getRelativePathToFile(string $pathToFileInProject): string
    {
        return str_replace($this->pathToProjectRoot, '', $this->getRealPath($pathToFileInProject));
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
            throw new RuntimeException('Override already exists for path ' . $relativePathToFileInProject);
        }
        $overridePath        =
            $this->getOverrideDirectoryForFile($relativePathToFileInProject) .
            '/' . $this->getFileNameNoExtensionForPathInProject($relativePathToFileInProject) .
            '.' . $this->getProjectFileHash($relativePathToFileInProject) .
            '.php.override';
        $pathToFileInProject = $this->pathToProjectRoot . '/' . $relativePathToFileInProject;
        if (false === is_file($pathToFileInProject)) {
            throw new RuntimeException('path ' . $pathToFileInProject . ' is not a file');
        }
        copy($pathToFileInProject, $overridePath);

        return $this->getRelativePathToFile($overridePath);
    }

    private function getOverrideForPath(string $relativePathToFileInProject): ?string
    {
        $fileDirectory       = $this->getOverrideDirectoryForFile($relativePathToFileInProject);
        $fileNameNoExtension = $this->getFileNameNoExtensionForPathInProject($relativePathToFileInProject);
        $filesInDirectory    = glob("$fileDirectory/$fileNameNoExtension*" . self::OVERRIDE_EXTENSION);
        if ([] === $filesInDirectory) {
            return null;
        }
        if (1 === count($filesInDirectory)) {
            return $fileDirectory . '/' . current($filesInDirectory);
        }
        throw new RuntimeException(
            'Found more than one override in path ' . $fileDirectory . ': '
            . print_r($filesInDirectory, true)
        );
    }

    private function getOverrideDirectoryForFile(string $relativePathToFileInProject): string
    {
        $path = $this->getPathToOverridesDirectory() . dirname($relativePathToFileInProject);
        if (!is_dir($path) && !(mkdir($path, 0777, true) && is_dir($path))) {
            throw new RuntimeException('Failed making override directory path ' . $path);
        }

        return $this->getRealPath($path);
    }

    private function getFileNameNoExtensionForPathInProject(string $relativePathToFileInProject): string
    {
        $fileName = basename($relativePathToFileInProject);

        return substr($fileName, 0, -self::EXTENSION_LENGTH_NO_HASH_IN_PROJECT);
    }

    private function getProjectFileHash(string $relativePathToFileInProject): string
    {
        return $this->getFileHash($this->pathToProjectRoot . '/' . $relativePathToFileInProject);
    }

    private function getFileHash(string $path): string
    {
        $contents = \ts\file_get_contents($path);

        return md5($contents);
    }

    /**
     * Loop over all the override files and update with the file contents from the project
     *
     * @param array|null $toUpdateRelativePathToFilesInProject
     *
     * @return array[] the file paths that have been updated
     */
    public function updateOverrideFiles(array $toUpdateRelativePathToFilesInProject): array
    {
        $filesUpdated = [];
        $filesSkipped = [];
        [$filesDifferent, $filesSame] = $this->compareOverridesWithProject();

        foreach ($filesDifferent as $fileDifferent) {
            $relativePathToFileInOverrides = $fileDifferent['overridePath'];
            $relativePathToFileInProject   = $fileDifferent['projectPath'];
            if (false === isset($toUpdateRelativePathToFilesInProject[$relativePathToFileInProject])) {
                $filesSkipped[] = $relativePathToFileInProject;
                continue;
            }
            $pathToFileInProject   = $this->pathToProjectRoot . $relativePathToFileInProject;
            $pathToFileInOverrides = $this->pathToProjectRoot . $relativePathToFileInOverrides;
            copy($pathToFileInProject, $pathToFileInOverrides);
            $filesUpdated[] = $relativePathToFileInProject;
        }

        return [
            $this->sortFiles($filesUpdated),
            $this->sortFiles($filesSkipped),
            $this->sortFiles($filesSame),
        ];
    }

    public function compareOverridesWithProject(): array
    {
        $fileSame       = [];
        $filesDifferent = [];
        foreach ($this->getOverridesIterator() as $pathToFileInOverrides) {
            $relativePathToFileInProject = $this->getRelativePathInProjectFromOverridePath($pathToFileInOverrides);
            if ($this->projectFileIsSameAsOverride($pathToFileInOverrides)) {
                $fileSame[] = $relativePathToFileInProject;
                continue;
            }
            $pathToFileInProject = $this->pathToProjectRoot . $relativePathToFileInProject;
            if (false === is_file($pathToFileInProject)) {
                throw new RuntimeException(
                    'path ' . $pathToFileInProject
                    . ' is not a file, the override should probably be removed, unless something else has gone wrong?'
                );
            }
            $relativePathToFileInOverrides = $this->getRelativePathToFile($pathToFileInOverrides);

            $filesDifferent[$relativePathToFileInProject]['overridePath'] = $relativePathToFileInOverrides;
            $filesDifferent[$relativePathToFileInProject]['projectPath']  = $relativePathToFileInProject;
            $filesDifferent[$relativePathToFileInProject]['diff']         = $this->getDiff(
                $relativePathToFileInProject,
                $relativePathToFileInOverrides
            );
        }

        return [$this->sortFilesByKey($filesDifferent), $this->sortFiles($fileSame)];
    }

    /**
     * Yield file paths in the override folder
     *
     * @return Generator|string[]
     */
    private function getOverridesIterator(): Generator
    {
        try {
            $recursiveIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $this->getPathToOverridesDirectory(),
                    RecursiveDirectoryIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($recursiveIterator as $fileInfo) {
                /**
                 * @var SplFileInfo $fileInfo
                 */
                if ($fileInfo->isFile()) {
                    if (
                        self::OVERRIDE_EXTENSION !== substr(
                            $fileInfo->getFilename(),
                            -strlen(self::OVERRIDE_EXTENSION)
                        )
                    ) {
                        continue;
                    }
                    $overridesPath = $fileInfo->getPathname();
                    $this->checkForDuplicateOverrides($overridesPath);
                    yield $overridesPath;
                }
            }
        } finally {
            $recursiveIterator = null;
            unset($recursiveIterator);
        }
    }

    private function checkForDuplicateOverrides(string $overridesPath): void
    {
        $overridesPathNoExtension = substr(
            $overridesPath,
            0,
            -self::EXTENSION_LENGTH_WITH_HASH_IN_OVERRIDES
        );

        $glob = glob($overridesPathNoExtension . '*.' . self::OVERRIDE_EXTENSION);
        if (count($glob) > 1) {
            $glob    = array_map('basename', $glob);
            $dirname = dirname($overridesPathNoExtension);
            throw new RuntimeException(
                "Found duplicated overrides in:\n\n$dirname\n\n"
                . print_r($glob, true)
                . "\n\nYou need to fix this so that there is only one override"
            );
        }
    }

    /**
     * Is the file in the project the same as the override file already?
     *
     * @param string $pathToFileInOverrides
     *
     * @return bool
     */
    private function projectFileIsSameAsOverride(string $pathToFileInOverrides): bool
    {
        $relativePathToFileInProject = $this->getRelativePathInProjectFromOverridePath($pathToFileInOverrides);

        return $this->getFileHash($this->pathToProjectRoot . '/' . $relativePathToFileInProject) ===
               $this->getFileHash($pathToFileInOverrides);
    }

    private function getDiff(
        string $relativePathToFileInProject,
        string $relativePathToFileInOverrides
    ): string {
        $diff = $this->differ->diff(
            \ts\file_get_contents($this->pathToProjectRoot . '/' . $relativePathToFileInOverrides),
            \ts\file_get_contents($this->pathToProjectRoot . '/' . $relativePathToFileInProject)
        );

        return <<<TEXT

-------------------------------------------------------------------------

Diff between:

+++ Project:  $relativePathToFileInProject
--- Override: $relativePathToFileInOverrides
 
$diff

-------------------------------------------------------------------------

TEXT;
    }

    private function sortFilesByKey(array $files): array
    {
        ksort($files, SORT_STRING);

        return $files;
    }

    private function sortFiles(array $files): array
    {
        sort($files, SORT_STRING);

        return $files;
    }

    /**
     * Before applying overrides, we can check for errors and then return useful information
     *
     * @return array
     */
    public function getInvalidOverrides(): array
    {
        $errors = [];
        foreach ($this->getOverridesIterator() as $pathToFileInOverrides) {
            if ($this->overrideFileHashIsCorrect($pathToFileInOverrides)) {
                continue;
            }
            if ($this->projectFileIsSameAsOverride($pathToFileInOverrides)) {
                continue;
            }
            $relativePathToFileInOverrides = $this->getRelativePathToFile($pathToFileInOverrides);
            $relativePathToFileInProject   =
                $this->getRelativePathInProjectFromOverridePath($pathToFileInOverrides);

            $errors[$relativePathToFileInOverrides]['overridePath'] = $relativePathToFileInOverrides;
            $errors[$relativePathToFileInOverrides]['projectPath']  = $relativePathToFileInProject;
            $errors[$relativePathToFileInOverrides]['diff']         = $this->getDiff(
                $relativePathToFileInProject,
                $relativePathToFileInOverrides
            );
            $errors[$relativePathToFileInOverrides]['new md5']      =
                $this->getProjectFileHash($relativePathToFileInProject);
        }

        return $errors;
    }

    private function overrideFileHashIsCorrect(string $pathToFileInOverrides): bool
    {
        $filenameParts = explode('.', basename($pathToFileInOverrides));
        if (4 !== count($filenameParts)) {
            throw new RuntimeException('Invalid override filename ' . $pathToFileInOverrides);
        }
        $hash                        = $filenameParts[1];
        $relativePathToFileInProject = $this->getRelativePathInProjectFromOverridePath($pathToFileInOverrides);

        return $hash === $this->getProjectFileHash($relativePathToFileInProject);
    }

    /**
     * Loop over all the override files and copy into the project
     *
     * @return array[] the file paths that have been updated
     */
    public function applyOverrides(): array
    {
        $filesUpdated = [];
        $filesSame    = [];
        $errors       = [];
        foreach ($this->getOverridesIterator() as $pathToFileInOverrides) {
            $relativePathToFileInProject   = $this->getRelativePathInProjectFromOverridePath($pathToFileInOverrides);
            $relativePathToFileInOverrides = $this->getRelativePathToFile($pathToFileInOverrides);
            if ($this->overrideFileHashIsCorrect($pathToFileInOverrides)) {
                if (false === is_file($pathToFileInOverrides)) {
                    throw new RuntimeException('path ' . $pathToFileInOverrides . ' is not a file');
                }
                copy($pathToFileInOverrides, $this->pathToProjectRoot . $relativePathToFileInProject);
                $filesUpdated[] = $relativePathToFileInProject;
                continue;
            }
            if ($this->projectFileIsSameAsOverride($pathToFileInOverrides)) {
                $filesSame[] = $relativePathToFileInProject;
                continue;
            }
            $errors[$pathToFileInOverrides]['diff']    = $this->getDiff(
                $relativePathToFileInProject,
                $relativePathToFileInOverrides
            );
            $errors[$pathToFileInOverrides]['new md5'] = $this->getProjectFileHash($relativePathToFileInProject);
        }
        if ([] !== $errors) {
            throw new RuntimeException('These file hashes were not up to date:' . print_r($errors, true));
        }

        return [$this->sortFiles($filesUpdated), $this->sortFiles($filesSame)];
    }
}
