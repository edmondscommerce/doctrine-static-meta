<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\GeneratedCode;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;

class GeneratedCodeTest extends AbstractTest
{

    const WORK_DIR = '/tmp/doctrine-static-meta-test-project/';

    const TEST_ENTITIES = [
        self::TEST_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Person',
        self::TEST_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Attributes\\Address',
        self::TEST_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Attributes\\Email',
        self::TEST_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Company',
        self::TEST_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Company\\Director'
    ];
    /**
     * @var string
     */
    private $workDir;

    private $phpNoXdebugFunction = <<<'BASH'
function phpNoXdebug {
    debugMode="$([[ "$-" == *x* ]] && echo 'on')";
    if [[ "$debugMode" == "on" ]]
    then
        set +x
    fi
    local temporaryPath="$(mktemp -t php.XXXX).ini"
    # Using awk to ensure that files ending without newlines do not lead to configuration error
    /usr/bin/php -i | grep "\.ini" | grep -o -e '\(/[a-z0-9._-]\+\)\+\.ini' | grep -v xdebug | xargs awk 'FNR==1{print ""}1' > "$temporaryPath"
    /usr/bin/php -n -c "$temporaryPath" "$@"
    rm -f "$temporaryPath"
    if [[ "$debugMode" == "on" ]]
    then
        set -x
    fi
}
BASH;


    protected function setupGeneratedDb()
    {
        $link = mysqli_connect($_SERVER['dbHost'], $_SERVER['dbUser'], $_SERVER['dbPass'], $_SERVER['dbName']);
        if (!$link) {
            throw new \Exception('Failed getting connection in ' . __METHOD__);
        }
        $generatedDbName = $_SERVER['dbName'] . '_generated';
        mysqli_query($link, "DROP DATABASE IF EXISTS $generatedDbName");
        mysqli_query($link, "CREATE DATABASE $generatedDbName ");
        mysqli_close($link);
        return $generatedDbName;
    }

    protected function initComposerAndInstall()
    {
        $vcsJson = '{"type":"vcs", "url":"' . realpath(__DIR__ . '/../../') . '"}';
        $bashCmds = <<<BASH
cd {$this->workDir}

{$this->phpNoXdebugFunction}
            
phpNoXdebug $(which composer) init -n \
    --repository='$vcsJson' \
    --require="edmondscommerce/doctrine-static-meta:dev-master" \
    --stability=dev

phpNoXdebug $(which composer) install \
    --prefer-dist

phpNoXdebug $(which composer) require phpunit/phpunit \
    --dev \
    --prefer-dist

phpNoXdebug $(which composer) dump-autoload --optimize
BASH;
        $this->execBash($bashCmds);
    }


    public function setup()
    {
        if (function_exists('xdebug_break')) {
            $this->markTestSkipped("Don't run this test with Xdebug enabled");
        }
        $this->clearWorkDir();
        $this->workDir = self::WORK_DIR;

        $generatedTestDbName = $this->setupGeneratedDb();

        $this->initComposerAndInstall();


        $fs = $this->getFileSystem();
        $fs->mkdir(self::WORK_DIR . '/src/');
        $fs->mkdir(self::WORK_DIR . '/src/Entities');
        $fs->mkdir(self::WORK_DIR . '/tests/');
        $fs->copy(__DIR__ . '/../bootstrap.php', self::WORK_DIR . '/tests/bootstrap.php');
        $fs->copy(__DIR__ . '/../../cli-config.php', self::WORK_DIR . '/cli-config.php');
        file_put_contents(self::WORK_DIR . '/.env', <<<EOF
export dbUser="{$_SERVER['dbUser']}"
export dbPass="{$_SERVER['dbPass']}"
export dbHost="{$_SERVER['dbHost']}"
export dbName="$generatedTestDbName"
EOF
        );
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $this->generateEntity($entityFqn);
        }
    }

    protected function generateEntity(string $entityFqn)
    {
        $namespace = self::TEST_NAMESPACE;
        $bash = <<< BASH
cd {$this->workDir}
        
{$this->phpNoXdebugFunction}

phpNoXdebug vendor/bin/doctrine dsm:generate:entity \
    --project-root-path="{$this->workDir}" \
    --project-root-namespace="{$namespace}" \
    --entity-fully-qualified-name="{$entityFqn}"
    
BASH;
        $this->execBash($bash);

    }

    protected function execBash(string $bashCmds)
    {
        exec(" set -xe; $bashCmds", $output, $exitCode);

        $this->assertEquals(
            0,
            $exitCode,
            "Error running bash commands:\n\nCommands:\n$bashCmds\n\nOutput:\n" . implode("\n", $output)
        );

    }

    public function testRunTests()
    {
        /** @lang bash */
        $bashCmds = <<<BASH
cd {$this->workDir}

{$this->phpNoXdebugFunction}

set +x
echo "

--------------------------------------------------
STARTS Running Tests In {$this->workDir}
--------------------------------------------------

"
set +x
phpNoXdebug vendor/bin/phpunit tests
set -x
echo "

--------------------------------------------------
DONE Running Tests In {$this->workDir}
--------------------------------------------------

"
set +x
BASH;
        $this->execBash($bashCmds);

    }
}
