<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Filesystem\Filesystem;

class PathHelper
{
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var FileCreationTransaction
     */
    protected $fileCreationTransaction;

    public function __construct(Filesystem $filesystem, FileCreationTransaction $fileCreationTransaction)
    {
        $this->filesystem              = $filesystem;
        $this->fileCreationTransaction = $fileCreationTransaction;
    }

    /**
     * @param string $pathToProjectRoot
     * @param array  $subDirectories
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function createSubDirectoriesAndGetPath(string $pathToProjectRoot, array $subDirectories): string
    {
        if (!$this->filesystem->exists($pathToProjectRoot)) {
            throw new DoctrineStaticMetaException("path to project root $pathToProjectRoot does not exist");
        }
        foreach ($subDirectories as $sd) {
            $pathToProjectRoot .= "/$sd";
            try {
                $this->filesystem->mkdir($pathToProjectRoot);
            } catch (\Exception $e) {
                throw new DoctrineStaticMetaException(
                    'Exception in '.__METHOD__.': '.$e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        }

        return \realpath($pathToProjectRoot);
    }

    /**
     * @param string $pathToProjectRoot
     * @param string $templatePath
     * @param string $destinationFileName
     * @param array  $subDirectories
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function copyTemplateAndGetPath(
        string $pathToProjectRoot,
        string $templatePath,
        string $destinationFileName,
        array $subDirectories
    ): string {
        try {
            $path = $this->createSubDirectoriesAndGetPath($pathToProjectRoot, $subDirectories);
            if (false === strpos($destinationFileName, '.php')) {
                $destinationFileName = "$destinationFileName.php";
            }
            $filePath = "$path/$destinationFileName";
            $this->filesystem->copy($templatePath, $filePath, true);
            $this->fileCreationTransaction::setPathCreated($filePath);

            return $filePath;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param string $pathToProjectRoot
     * @param string $templatePath
     * @param string $destPath
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function copyTemplateDirectoryAndGetPath(
        string $pathToProjectRoot,
        string $templatePath,
        string $destPath
    ): string {
        $realTemplatePath = realpath($templatePath);
        if (false === $realTemplatePath) {
            throw new DoctrineStaticMetaException('path '.$templatePath.' does not exist');
        }

        $relativeDestPath = $this->filesystem->makePathRelative($destPath, $pathToProjectRoot);
        $subDirectories   = explode('/', $relativeDestPath);
        $path             = $this->createSubDirectoriesAndGetPath($pathToProjectRoot, $subDirectories);
        $this->filesystem->mirror($realTemplatePath, $path);
        $this->fileCreationTransaction::setPathCreated($path);

        return $path;
    }

    /**
     * Move the basename of a path to the find/replaced version
     *
     * Then return the updated path
     *
     * @param string $find
     * @param string $replace
     * @param string $path
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function renamePathBasename(string $find, string $replace, string $path): string
    {
        $basename    = basename($path);
        $newBasename = str_replace($find, $replace, $basename);
        $moveTo      = \dirname($path).'/'.$newBasename;
        if ($moveTo === $path) {
            return $path;
        }
        if (is_dir($moveTo) || file_exists($moveTo)) {
            throw new DoctrineStaticMetaException(
                "Error trying to move:\n[$path]\n to \n[$moveTo]\ndestination already exists"
            );
        }
        try {
            $this->filesystem->rename($path, $moveTo);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }

        return $moveTo;
    }

    /**
     * @param string $path
     * @param string $singular
     * @param string $plural
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function renamePathBasenameSingularOrPlural(
        string $path,
        string $singular,
        string $plural
    ): string {
        $find     = AbstractGenerator::FIND_ENTITY_NAME;
        $replace  = $singular;
        $basename = \basename($path);
        if (false !== \strpos($basename, AbstractGenerator::FIND_ENTITY_NAME_PLURAL)) {
            $find    = AbstractGenerator::FIND_ENTITY_NAME_PLURAL;
            $replace = $plural;
        }

        return $this->renamePathBasename($find, $replace, $path);
    }

    public function getPathFromNameAndSubDirs(string $pathToProjectRoot, string $name, array $subDirectories): string
    {
        $path = realpath($pathToProjectRoot).'/'.implode('/', $subDirectories).'/'.$name.'.php';

        return $path;
    }
}