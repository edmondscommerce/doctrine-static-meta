<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\GeneratedCode;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

class GeneratedCodeTest extends AbstractTest
{

    const WORK_DIR = self::CHECKED_OUT_PROJECT_ROOT_PATH . '/GeneratedCodeTest';

    const BASH_PHPNOXDEBUG_FUNCTION_FILE_PATH = '/tmp/phpNoXdebugFunction.bash';

    const TEST_ENTITY_PERSON        = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Person';
    const TEST_ENTITY_ADDRESS       = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Attributes\\Address';
    const TEST_ENTITY_EMAIL         = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Attributes\\Email';
    const TEST_ENTITY_COMPANY       = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Company';
    const TEST_ENTITY_DIRECTOR      = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Company\\Director';
    const TEST_ENTITY_ORDER         = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Order';
    const TEST_ENTITY_ORDER_ADDRESS = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Order\\Address';

    const TEST_ENTITIES = [
        self::TEST_ENTITY_PERSON,
        self::TEST_ENTITY_ADDRESS,
        self::TEST_ENTITY_EMAIL,
        self::TEST_ENTITY_COMPANY,
        self::TEST_ENTITY_DIRECTOR,
        self::TEST_ENTITY_ORDER,
        self::TEST_ENTITY_ORDER_ADDRESS
    ];

    const TEST_RELATIONS = [
        [self::TEST_ENTITY_PERSON, RelationsGenerator::HAS_MANY_TO_ONE, self::TEST_ENTITY_ADDRESS],
        [self::TEST_ENTITY_PERSON, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_EMAIL],
        [self::TEST_ENTITY_COMPANY, RelationsGenerator::HAS_MANY_TO_MANY, self::TEST_ENTITY_DIRECTOR],
        [self::TEST_ENTITY_COMPANY, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_ADDRESS],
        [self::TEST_ENTITY_COMPANY, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_EMAIL],
        [self::TEST_ENTITY_DIRECTOR, RelationsGenerator::HAS_ONE_TO_ONE, self::TEST_ENTITY_PERSON],
        [self::TEST_ENTITY_ORDER, RelationsGenerator::HAS_MANY_TO_ONE, self::TEST_ENTITY_PERSON],
        [self::TEST_ENTITY_ORDER, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_ORDER_ADDRESS],
        [self::TEST_ENTITY_ORDER_ADDRESS, RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_ONE, self::TEST_ENTITY_ADDRESS]
    ];

    /**
     * @var string
     */
    private $workDir;

    const BASH_PHPNOXDEBUG_FUNCTION = <<<'BASH'
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


    /**
     * @return string Generated Database Name
     * @throws \Exception
     */
    protected function setupGeneratedDb(): string
    {
        $link = mysqli_connect($_SERVER['dbHost'], $_SERVER['dbUser'], $_SERVER['dbPass'], $_SERVER['dbName']);
        if (!$link) {
            throw new \Exception('Failed getting connection in ' . __METHOD__);
        }
        $generatedDbName = $_SERVER['dbName'] . '_generated';
        mysqli_query($link, "DROP DATABASE IF EXISTS $generatedDbName");
        mysqli_query($link, "CREATE DATABASE $generatedDbName CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
        mysqli_close($link);
        $dbHost      = $_SERVER['dbHost'];
        $dbUser      = $_SERVER['dbUser'];
        $dbPass      = $_SERVER['dbPass'];
        $rebuildBash = <<<BASH
echo "Dropping and creating the DB $generatedDbName"        
mysql -u $dbUser -p$dbPass -h $dbHost -e "DROP DATABASE IF EXISTS $generatedDbName";
mysql -u $dbUser -p$dbPass -h $dbHost -e "CREATE DATABASE $generatedDbName CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci";
BASH;
        $this->addToRebuildFile($rebuildBash);
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
    "phpunit/phpunit": "^6.3",
    "fzaninotto/faker": "^1.7"
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

        file_put_contents(self::BASH_PHPNOXDEBUG_FUNCTION_FILE_PATH, self::BASH_PHPNOXDEBUG_FUNCTION);

        $bashCmds = <<<BASH
           
phpNoXdebug $(which composer) install \
    --prefer-dist

phpNoXdebug $(which composer) dump-autoload --optimize

BASH;
        $this->execBash($bashCmds);
    }


    protected function initRebuildFile()
    {

        $this->addToRebuildFile(
            <<<'BASH'
#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
echo "
===========================================
$(hostname) $0 $@
===========================================
"
# Error Handling
backTraceExit () {
    local err=$?
    set +o xtrace
    local code="${1:-1}"
    printf "\n\nError in ${BASH_SOURCE[1]}:${BASH_LINENO[0]}. '${BASH_COMMAND}'\n\n exited with status: \n\n$err\n\n"
    # Print out the stack trace described by $function_stack
    if [ ${#FUNCNAME[@]} -gt 2 ]
    then
        echo "Call tree:"
        for ((i=1;i<${#FUNCNAME[@]}-1;i++))
        do
            echo " $i: ${BASH_SOURCE[$i+1]}:${BASH_LINENO[$i]} ${FUNCNAME[$i]}(...)"
        done
    fi
    echo "Exiting with status ${code}"
    exit "${code}"
}
trap 'backTraceExit' ERR
set -o errtrace
# Error Handling Ends

echo "clearing out generated code"
rm -rf src/* tests/*

echo "preparing empty Entities directory"
mkdir src/Entities

echo "making sure we have the latest version of code"
(cd vendor/edmondscommerce/doctrine-static-meta && git pull)

BASH
            ,
            false
        );
    }


    public function setup()
    {
        SimpleEnv::setEnv(Config::getProjectRootDirectory() . '/.env');
        $this->clearWorkDir();
        $this->workDir = self::WORK_DIR;
        $this->initRebuildFile();
        $generatedTestDbName = $this->setupGeneratedDb();
        $this->initComposerAndInstall();
        $fs = $this->getFileSystem();
        $fs->mkdir(self::WORK_DIR . '/src/');
        $fs->mkdir(self::WORK_DIR . '/src/Entities');
        $fs->mkdir(self::WORK_DIR . '/tests/');
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

        $this->addToRebuildFile(self::BASH_PHPNOXDEBUG_FUNCTION);
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $this->generateEntity($entityFqn);
        }
        foreach (self::TEST_RELATIONS as $relation) {
            $this->setRelation(...$relation);
        }
        $this->execDoctrine(' orm:schema-tool:create');
    }

    /**
     * @param string $bash
     * @param bool   $append
     *
     * @return bool
     * @throws \Exception
     */
    protected function addToRebuildFile(string $bash, $append = true): bool
    {
        if ($append) {
            $result = file_put_contents(
                self::WORK_DIR . '/rebuild.bash',
                "\n\n" . $bash,
                FILE_APPEND
            );
        } else {
            $result = file_put_contents(
                self::WORK_DIR . '/rebuild.bash',
                $bash
            );
        }
        if (!$result) {
            throw new \RuntimeException('Failed writing to rebuild file');
        }
        return true;
    }

    protected function setRelation(string $entity1, string $type, string $entity2)
    {
        $namespace = self::TEST_PROJECT_ROOT_NAMESPACE;
        $this->execDoctrine(
            <<<DOCTRINE
dsm:set:relation \
    --project-root-path="{$this->workDir}" \
    --project-root-namespace="{$namespace}" \
    --entity1="{$entity1}" \
    --hasType="{$type}" \
    --entity2="{$entity2}"    
DOCTRINE
        );
    }

    protected function execDoctrine(string $doctrineCmds)
    {
        $bash    = <<<BASH
phpNoXdebug vendor/bin/doctrine $doctrineCmds    
BASH;
        $error   = false;
        $message = '';
        try {
            $this->execBash($bash);
        } catch (\RuntimeException $e) {
            $this->addToRebuildFile("\n\nexit 0;\n\n#The command below failed...\n\n");
            $error   = true;
            $message = $e->getMessage();
        }
        $this->addToRebuildFile($bash);
        $this->assertFalse($error, $message);
    }

    protected function generateEntity(string $entityFqn)
    {
        $doctrineCmd = <<<DOCTRINE
 dsm:generate:entity \
    --entity-fully-qualified-name="{$entityFqn}"
DOCTRINE;
        $this->execDoctrine($doctrineCmd);

    }

    /**
     * Runs bash with strict error handling and verbose logging
     *
     * Will ensure the phpNoXdebugFunction is available and will CD into the correct directory before running commands
     *
     * Asserts that the command returns with an exit code of 0
     *
     * Appends to the rebuild file allowing easy rerunning of the commmands in the test project
     *
     * @param string $bashCmds
     *
     * @throws \Exception
     */
    protected function execBash(string $bashCmds)
    {
        fwrite(STDERR, "\n\t# Executing:\n$bashCmds");
        $startTime = microtime(true);
        $process   = proc_open(
            "source " . self::BASH_PHPNOXDEBUG_FUNCTION_FILE_PATH . "; cd {$this->workDir}; set -xe;  $bashCmds",
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
        if (0 !== $exitCode) {
            throw new \RuntimeException(
                "Error running bash commands:\n\nstderr:\n----------\n\n"
                . str_replace(
                    "\n",
                    "\n\t",
                    "\n$stderr"
                )
                . "\n\nstdout:\n----------\n"
                . str_replace(
                    "\n",
                    "\n\t",
                    "\n$stdout"
                )
                . "\n\nCommands:\n----------\n"
                . str_replace(
                    "\n",
                    "\n\t",
                    "\n$bashCmds"
                ) . "\n\n"
            );
        }
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
        $this->execBash($bashCmds);

    }
}
