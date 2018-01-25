<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\GeneratedCode;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

class GeneratedCodeTest extends AbstractTest
{

    const WORK_DIR = '/tmp/doctrine-static-meta-test-project/';

    const TEST_ENTITY_PERSON   = self::TEST_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Person';
    const TEST_ENTITY_ADDRESS  = self::TEST_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Attributes\\Address';
    const TEST_ENTITY_EMAIL    = self::TEST_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Attributes\\Email';
    const TEST_ENTITY_COMPANY  = self::TEST_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Company';
    const TEST_ENTITY_DIRECTOR = self::TEST_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Company\\Director';

    const TEST_ENTITIES = [
        self::TEST_ENTITY_PERSON,
        self::TEST_ENTITY_ADDRESS,
        self::TEST_ENTITY_EMAIL,
        self::TEST_ENTITY_COMPANY,
        self::TEST_ENTITY_DIRECTOR,
    ];
    /**
     * @var string
     */
    private $workDir;

    private $phpNoXdebugFunction = <<<'BASH'
function phpNoXdebug {
    debugMode="off"
    if [[ "$-" == *x* ]]
    then
        debugMode='on'
    fi
    if [[ "$debugMode" == "on" ]]
    then
        set +x
    fi
    local returnCode;
    local temporaryPath="$(mktemp -t php.XXXX).ini"
    # Using awk to ensure that files ending without newlines do not lead to configuration error
    /usr/bin/php -i | grep "\.ini" | grep -o -e '\(/[a-z0-9._-]\+\)\+\.ini' | grep -v xdebug | xargs awk 'FNR==1{print ""}1' > "$temporaryPath"
    #Run PHP with temp config with no xdebug, display errors on stderr
    set +e
    /usr/bin/php -n -c "$temporaryPath" "$@"    
    returnCode=$?
    set -e
    rm -f "$temporaryPath"
    if [[ "$debugMode" == "on" ]]
    then
        set -x
    fi
    return $returnCode;    
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
        mysqli_query($link, "CREATE DATABASE $generatedDbName CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
        mysqli_close($link);
        return $generatedDbName;
    }

    protected function initComposerAndInstall()
    {
        $vcsPath = realpath(__DIR__ . '/../../');

        $composerJson = <<<'JSON'
{
  "require": {
    "edmondscommerce/doctrine-static-meta": "dev-master"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "%s"
    }
  ],
  "minimum-stability": "dev",
  "require-dev": {
    "phpunit/phpunit": "^7.0@dev",
    "fzaninotto/faker": "^1.8@dev"
  },
  "autoload": {
    "psr-4": {
      "DSM\\Test\\Project\\": [
        "src/"
      ]
    }
  },
  "autoload-dev": {
    "psr-4": {
      "DSM\\Test\\Project\\": [
        "tests/"
      ]
    }
  }
}

JSON;
        file_put_contents($this->workDir . '/composer.json', sprintf($composerJson, $vcsPath));


        $bashCmds = <<<BASH
           
phpNoXdebug $(which composer) install \
    --prefer-dist

phpNoXdebug $(which composer) require phpunit/phpunit \
    --dev \
    --prefer-dist \
    --prefer-stable
    
    
phpNoXdebug $(which composer) require fzaninotto/faker \
    --dev \
    --prefer-dist \
    --prefer-stable    

phpNoXdebug $(which composer) dump-autoload --optimize

BASH;
        $this->execBash($bashCmds, __FUNCTION__);
    }


    public function setup()
    {
//        if (function_exists('xdebug_break')) {
//            $this->markTestSkipped("Don't run this test with Xdebug enabled");
//        }
        SimpleEnv::setEnv(Config::getProjectRootDirectory() . '/.env');

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
        $fs->copy(__DIR__ . '/../../phpunit.xml', self::WORK_DIR . '/phpunit.xml');
        file_put_contents(
            self::WORK_DIR . '/.env', <<<EOF
export dbUser="{$_SERVER['dbUser']}"
export dbPass="{$_SERVER['dbPass']}"
export dbHost="{$_SERVER['dbHost']}"
export dbName="$generatedTestDbName"
EOF
        );
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $this->generateEntity($entityFqn);
        }
        $this->setRelation(
            self::TEST_ENTITY_PERSON,
            RelationsGenerator::HAS_ONE_TO_MANY,
            self::TEST_ENTITY_ADDRESS
        );
        $this->setRelation(
            self::TEST_ENTITY_PERSON,
            RelationsGenerator::HAS_ONE_TO_ONE,
            self::TEST_ENTITY_EMAIL
        );
        $this->setRelation(
            self::TEST_ENTITY_COMPANY,
            RelationsGenerator::HAS_ONE_TO_MANY,
            self::TEST_ENTITY_DIRECTOR
        );
        $this->setRelation(
            self::TEST_ENTITY_PERSON,
            RelationsGenerator::HAS_ONE_TO_MANY,
            self::TEST_ENTITY_DIRECTOR
        );
        $this->execDoctrine(' orm:schema-tool:create');
    }

    protected function setRelation(string $entity1, string $type, string $entity2)
    {
        $namespace = self::TEST_NAMESPACE;
        $this->execDoctrine(
            <<<DOCTRINE
dsm:set:relation \
    --project-root-path="{$this->workDir}" \
    --project-root-namespace="{$namespace}" \
    --entity1="{$entity1}" \
    --type="{$type}" \
    --entity2="{$entity2}"    
DOCTRINE
        );
    }

    protected function execDoctrine(string $doctrineCmds)
    {
        $bash = <<<BASH
phpNoXdebug vendor/bin/doctrine $doctrineCmds    
BASH;
        $this->execBash($bash);
    }

    protected function generateEntity(string $entityFqn)
    {
        $namespace   = self::TEST_NAMESPACE;
        $doctrineCmd = <<<DOCTRINE
 dsm:generate:entity \
    --project-root-path="{$this->workDir}" \
    --project-root-namespace="{$namespace}" \
    --entity-fully-qualified-name="{$entityFqn}"
DOCTRINE;
        $this->execDoctrine($doctrineCmd);

    }

    /**
     * Runs bash with strict error handling and verbose logging
     *
     * Asserts that the command returns with an exit code of 0
     *
     * @param string $bashCmds
     */
    protected function execBash(string $bashCmds)
    {
        $phpNoXdebugFunctionPath = '/tmp/phpNoXdebugFunction.bash';
        if (!file_exists($phpNoXdebugFunctionPath)) {
            file_put_contents($phpNoXdebugFunctionPath, $this->phpNoXdebugFunction);
        }
        fwrite(STDERR, "\n\t# Executing:\n$bashCmds");
        $startTime = microtime(true);
        $process   = proc_open(
            "source $phpNoXdebugFunctionPath; cd {$this->workDir}; set -xe;  $bashCmds",
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes
        );
        $stdout    = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
        $this->assertEquals(
            0,
            $exitCode,
            str_replace(
                "\n",
                "\n\t",
                "Error running bash commands:\n\nstderr:\n----------\n\n$stderr\n\nstdout:\n----------\n\n$stdout\n\nCommands:\n----------\n$bashCmds\n\n"
            )
        );
        $seconds = round(microtime(true) - $startTime, 2);
        fwrite(STDERR, "\n\t\t#Completed in $seconds seconds\n");
    }

    public function testRunTests()
    {
        /** @lang bash */
        $bashCmds = <<<BASH

set +x
echo "

--------------------------------------------------
STARTS Running Tests In {$this->workDir}
--------------------------------------------------

"
set -x
phpNoXdebug vendor/bin/phpunit tests 2>&1
set +x
echo "

--------------------------------------------------
DONE Running Tests In {$this->workDir}
--------------------------------------------------

"
set -x
BASH;
        $this->execBash($bashCmds, __FUNCTION__);

    }
}
