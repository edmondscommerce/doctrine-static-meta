<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use Overtrue\PHPLint\Linter;

class CodeValidator
{
    private $pathToWorkDir;
    private $relativePathToProjectRoot;
    private $namespaceRoot;

    public function __invoke(string $pathToWorkDir, string $namespaceRoot): ?string
    {
        $this->namespaceRoot = $namespaceRoot;
        $this->setRealPathToWorkDir($pathToWorkDir);
        $this->setRelativePathFromWorkDirToProjectRoot();
        $this->writePhpStanAutoloader();
        $lintErrors = $this->runPhpLint();
        if (null !== $lintErrors) {
            return $lintErrors;
        }
        $stanErrors = $this->runPhpStan();
        if (null !== $stanErrors) {
            return $stanErrors;
        }

        return null;
    }

    private function runPhpLint()
    {
        $exclude    = ['vendor'];
        $extensions = ['php'];
        $linter     = new Linter($this->pathToWorkDir, $exclude, $extensions);
        $lint       = $linter->lint([], false);
        if (empty($lint)) {
            return null;
        }
        $message = str_replace($this->pathToWorkDir, '', print_r($lint, true));

        return "\n\nPHP Syntax Errors in $this->pathToWorkDir\n\n$message\n\n";
    }

    private function setRealPathToWorkDir(string $pathToWorkDir): void
    {
        $realpath = \realpath($pathToWorkDir);
        if (false === $realpath) {
            throw new \RuntimeException('Path '.$pathToWorkDir.' does not exist');
        }
        $this->pathToWorkDir = $realpath;
    }

    private function setRelativePathFromWorkDirToProjectRoot()
    {
        $currentPath  = $this->pathToWorkDir;
        $relativePath = './';
        while (false === is_dir("$currentPath/$relativePath/vendor")) {
            $relativePath .= '../';
            if (false === \realpath("$currentPath/$relativePath")) {
                throw new \RuntimeException('Failed finding the relative path to the vendor directory');
            }
        }
        $this->relativePathToProjectRoot = $relativePath;
    }

    private function writePhpStanAutoloader(): void
    {
        $phpstanNamespace  = $this->namespaceRoot.'\\\\';
        $phpstanFolder     = $this->pathToWorkDir.'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER;
        $phpstanAutoLoader = '<?php declare(strict_types=1);
require __DIR__."'.$this->relativePathToProjectRoot.'/vendor/autoload.php";

use Composer\Autoload\ClassLoader;

$loader = new class extends ClassLoader
        {
            public function loadClass($class)
            {
                if (false === strpos($class, "'.$this->namespaceRoot.'")) {
                    return false;
                }
                $found = parent::loadClass($class);
                if (\in_array(gettype($found), [\'boolean\', \'NULL\'], true)) {
                    //good spot to set a break point ;)
                    return false;
                }

                return true;
            }
        };
        $loader->addPsr4(
            "'.$phpstanNamespace.'","'.$phpstanFolder.'"
        );
        $loader->register();
';
        file_put_contents($this->pathToWorkDir.'/phpstan-autoloader.php', $phpstanAutoLoader);
    }

    private function runPhpStan(): ?string
    {
        $phpstanCommand = FullProjectBuildFunctionalTest::BASH_PHPNOXDEBUG_FUNCTION
                          ."\n\nphpNoXdebug bin/phpstan.phar analyse $this->pathToWorkDir/src -l7 -a "
                          .$this->pathToWorkDir.'/phpstan-autoloader.php 2>&1';
        if ($this->isTravis()) {
            $phpstanCommand = "bin/phpstan.phar analyse $this->pathToWorkDir/src -l7 -a "
                              .$this->pathToWorkDir.'/phpstan-autoloader.php 2>&1';
        }
        exec(
            $phpstanCommand,
            $output,
            $exitCode
        );
        if (0 === $exitCode) {
            return null;
        }

        return 'PHPStan errors found in generated code at '.$this->pathToWorkDir
               .':'."\n\n".implode("\n", $output);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return bool
     */
    protected function isTravis(): bool
    {
        return isset($_SERVER['TRAVIS']);
    }
}
