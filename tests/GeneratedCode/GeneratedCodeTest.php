<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\GeneratedCode;

use Doctrine\Common\Inflector\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\PHPQA\Constants;

class GeneratedCodeTest extends AbstractTest
{
    public const TEST_PROJECT_ROOT_NAMESPACE = 'DSM\\GeneratedCodeTest\\Project';

    public const TEST_ENTITY_NAMESPACE_BASE = self::TEST_PROJECT_ROOT_NAMESPACE
                                              .'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME;

    public const TEST_ENTITY_PERSON        = self::TEST_ENTITY_NAMESPACE_BASE.'\\Person';
    public const TEST_ENTITY_ADDRESS       = self::TEST_ENTITY_NAMESPACE_BASE.'\\Attributes\\Address';
    public const TEST_ENTITY_EMAIL         = self::TEST_ENTITY_NAMESPACE_BASE.'\\Attributes\\Email';
    public const TEST_ENTITY_COMPANY       = self::TEST_ENTITY_NAMESPACE_BASE.'\\Company';
    public const TEST_ENTITY_DIRECTOR      = self::TEST_ENTITY_NAMESPACE_BASE.'\\Company\\Director';
    public const TEST_ENTITY_ORDER         = self::TEST_ENTITY_NAMESPACE_BASE.'\\Order';
    public const TEST_ENTITY_ORDER_ADDRESS = self::TEST_ENTITY_NAMESPACE_BASE.'\\Order\\Address';

    public const TEST_ENTITY_NAME_SPACING_COMPANY        = self::TEST_ENTITY_NAMESPACE_BASE.'\\Company';
    public const TEST_ENTITY_NAME_SPACING_SOME_CLIENT    = self::TEST_ENTITY_NAMESPACE_BASE.'\\Some\\Client';
    public const TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT = self::TEST_ENTITY_NAMESPACE_BASE.'\\Another\\Client';

    public const TEST_ENTITIES = [
        self::TEST_ENTITY_PERSON,
        self::TEST_ENTITY_ADDRESS,
        self::TEST_ENTITY_EMAIL,
        self::TEST_ENTITY_COMPANY,
        self::TEST_ENTITY_DIRECTOR,
        self::TEST_ENTITY_ORDER,
        self::TEST_ENTITY_ORDER_ADDRESS,
        self::TEST_ENTITY_NAME_SPACING_COMPANY,
        self::TEST_ENTITY_NAME_SPACING_SOME_CLIENT,
        self::TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT
    ];

    public const TEST_RELATIONS = [
        [self::TEST_ENTITY_PERSON, RelationsGenerator::HAS_UNIDIRECTIONAL_MANY_TO_ONE, self::TEST_ENTITY_ADDRESS],
        [self::TEST_ENTITY_PERSON, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_EMAIL],
        [self::TEST_ENTITY_COMPANY, RelationsGenerator::HAS_MANY_TO_MANY, self::TEST_ENTITY_DIRECTOR],
        [self::TEST_ENTITY_COMPANY, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_ADDRESS],
        [self::TEST_ENTITY_COMPANY, RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_MANY, self::TEST_ENTITY_EMAIL],
        [self::TEST_ENTITY_DIRECTOR, RelationsGenerator::HAS_ONE_TO_ONE, self::TEST_ENTITY_PERSON],
        [self::TEST_ENTITY_ORDER, RelationsGenerator::HAS_MANY_TO_ONE, self::TEST_ENTITY_PERSON],
        [self::TEST_ENTITY_ORDER, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_ORDER_ADDRESS],
        [self::TEST_ENTITY_ORDER_ADDRESS, RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_ONE, self::TEST_ENTITY_ADDRESS],
        [
            self::TEST_ENTITY_NAME_SPACING_COMPANY,
            RelationsGenerator::HAS_ONE_TO_MANY,
            self::TEST_ENTITY_NAME_SPACING_SOME_CLIENT
        ],
        [
            self::TEST_ENTITY_NAME_SPACING_COMPANY,
            RelationsGenerator::HAS_ONE_TO_MANY,
            self::TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT
        ],
    ];

    public const TEST_FIELD_NAMESPACE_BASE = self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entity\\Fields';

    protected function assertWeCheckAllPossibleRelationTypes()
    {
        $included = $toTest = [];
        foreach (RelationsGenerator::HAS_TYPES as $hasType) {
            if (0 === \strpos($hasType, RelationsGenerator::PREFIX_INVERSE)) {
                continue;
            }
            $toTest[$hasType] = true;
        }
        \ksort($toTest);
        foreach (self::TEST_RELATIONS as $relation) {
            $included[$relation[1]] = true;
        }
        \ksort($included);
        $missing = \array_diff(\array_keys($toTest), \array_keys($included));
        $this->assertEmpty(
            $missing,
            'We are not testing all relation types - '
            .'these ones have not been included: '
            .print_r($missing, true)
        );
    }

    public const BASH_PHPNOXDEBUG_FUNCTION = <<<'BASH'
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
    /usr/bin/php -i | grep "\.ini" | grep -o -e '\(/[a-z0-9._-]\+\)\+\.ini' | grep -v xdebug \
        | xargs awk 'FNR==1{print ""}1' > "$temporaryPath"
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
    return ${returnCode};    
}

BASH;
    /**
     * @var string
     */
    private $workDir;

    /**
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function setup()
    {
        if (isset($_SERVER[Constants::QA_QUICK_TESTS_KEY])
            && (int)$_SERVER[Constants::QA_QUICK_TESTS_KEY] === Constants::QA_QUICK_TESTS_ENABLED
        ) {
            return;
        }
        $this->workDir      = $this->isTravis() ?
            AbstractTest::VAR_PATH.'/GeneratedCodeTest'
            : sys_get_temp_dir().'/dsm/test-project';
        $this->entitiesPath = $this->workDir.'/src/Entities';
        $this->getFileSystem()->mkdir($this->workDir);
        $this->emptyDirectory($this->workDir);
        $this->getFileSystem()->mkdir($this->entitiesPath);
        $this->setupContainer();
        $this->initRebuildFile();
        $this->setupGeneratedDb();
        $this->initComposerAndInstall();
        $fileSystem = $this->getFileSystem();
        $fileSystem->mkdir(
            [
                $this->workDir.'/tests/',
                $this->workDir.'/cache/Proxies',
                $this->workDir.'/cache/qa',
                $this->workDir.'/qaConfig',
            ]
        );
        $fileSystem->copy(
            __DIR__.'/../../qaConfig/phpunit.xml',
            $this->workDir.'/qaConfig/phpunit.xml'
        );
        $fileSystem->copy(
            __DIR__.'/../../qaConfig/phpunit-with-coverage.xml',
            $this->workDir.'/qaConfig/phpunit-with-coverage.xml'
        );
        $fileSystem->copy(__DIR__.'/../../cli-config.php', $this->workDir.'/cli-config.php');
        file_put_contents($this->workDir.'/README.md', '#Generated Code');

        $this->addToRebuildFile(self::BASH_PHPNOXDEBUG_FUNCTION);

        $entities              = $this->generateEntities();
        $standardFieldEntities = $this->generateStandardFieldEntities();
        $this->generateRelations();
        $this->generateFields();
        $this->setFields(
            $entities,
            $this->getFieldFqns(MappingHelper::COMMON_TYPES)
        );
        $this->setFields(
            $standardFieldEntities,
            $this->getFieldFqns(FieldGenerator::STANDARD_FIELDS)
        );
    }

    protected function initRebuildFile()
    {
        $bash =
            <<<'BASH'
#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd "$DIR";
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

BASH;
        if (!$this->isTravis()) {
            $bash .= self::BASH_PHPNOXDEBUG_FUNCTION;
        }
        file_put_contents(
            $this->workDir.'/rebuild.bash',
            "\n\n".$bash
        );
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function isTravis(): bool
    {
        return isset($_SERVER['TRAVIS']);
    }

    /**
     * @return string Generated Database Name
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function setupGeneratedDb(): string
    {
        $dbHost = $this->container->get(Config::class)->get(ConfigInterface::PARAM_DB_HOST);
        $dbUser = $this->container->get(Config::class)->get(ConfigInterface::PARAM_DB_USER);
        $dbPass = $this->container->get(Config::class)->get(ConfigInterface::PARAM_DB_PASS);
        $dbName = $this->container->get(Config::class)->get(ConfigInterface::PARAM_DB_NAME);
        $link   = mysqli_connect($dbHost, $dbUser, $dbPass);
        if (!$link) {
            throw new DoctrineStaticMetaException('Failed getting connection in '.__METHOD__);
        }
        $generatedDbName = $dbName.'_generated';
        mysqli_query($link, "DROP DATABASE IF EXISTS $generatedDbName");
        mysqli_query($link, "CREATE DATABASE $generatedDbName 
        CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
        mysqli_close($link);

        $rebuildBash = <<<BASH
echo "Dropping and creating the DB $generatedDbName"        
mysql -u $dbUser -p$dbPass -h $dbHost -e "DROP DATABASE IF EXISTS $generatedDbName";
mysql -u $dbUser -p$dbPass -h $dbHost -e "CREATE DATABASE $generatedDbName 
CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci";
BASH;
        $this->addToRebuildFile($rebuildBash);
        file_put_contents(
            $this->workDir.'/.env',
            <<<EOF
export dbUser="{$dbUser}"
export dbPass="{$dbPass}"
export dbHost="{$dbHost}"
export dbName="$generatedDbName"
EOF
        );

        return $generatedDbName;
    }

    /**
     * @param string $bash
     *
     * @return bool
     * @throws \Exception
     */
    protected function addToRebuildFile(string $bash): bool
    {
        $result = file_put_contents(
            $this->workDir.'/rebuild.bash',
            "\n\n".$bash."\n\n",
            FILE_APPEND
        );
        if (!$result) {
            throw new \RuntimeException('Failed writing to rebuild file');
        }

        return true;
    }

    protected function initComposerAndInstall()
    {
        $vcsPath = realpath(__DIR__.'/../../');

        $composerJson = <<<'JSON'
{
  "require": {
    "edmondscommerce/doctrine-static-meta": "dev-%s"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "%s"
    },
    {
      "type": "vcs",
      "url": "https://github.com/edmondscommerce/Faker.git",
      "no-api": true
    }
  ],
  "minimum-stability": "dev",
  "require-dev": {
    "phpunit/phpunit": "^6.3",
    "fzaninotto/faker": "^1.7",
    "edmondscommerce/phpqa": "dev-master@dev"
  },
  "autoload": {
    "psr-4": {
      "DSM\\GeneratedCodeTest\\Project\\": [
        "src/"
      ]
    }
  },
  "autoload-dev": {
    "psr-4": {
      "DSM\\GeneratedCodeTest\\Project\\": [
        "tests/"
      ]
    }
  },
  "config": {
    "bin-dir": "bin",
    "preferred-install": {
      "edmondscommerce/*": "source",
      "*": "dist"
    },
    "optimize-autoloader": true
  }
}
JSON;

        $gitCurrentBranchName = trim(shell_exec("git branch | grep '*' | cut -d ' ' -f 2"));
        file_put_contents(
            $this->workDir.'/composer.json',
            sprintf($composerJson, $gitCurrentBranchName, $vcsPath)
        );

        $phpCmd   = $this->isTravis() ? 'php' : 'phpNoXdebug';
        $bashCmds = <<<BASH

cat composer.json           
           
$phpCmd $(which composer) install \
    --prefer-dist -vvv

$phpCmd $(which composer) dump-autoload --optimize

BASH;
        $this->execBash($bashCmds);
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
        $fullCmds  = '';
        if (!$this->isTravis()) {
            $fullCmds .= "\n".self::BASH_PHPNOXDEBUG_FUNCTION;
        }
        $fullCmds .= "\n\ncd {$this->workDir}; set -xe;  $bashCmds";
        $process  = proc_open(
            $fullCmds,
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes
        );
        $stdout   = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
        if (0 !== $exitCode) {
            throw new \RuntimeException(
                "Error running bash commands:\n\nstderr:\n----------\n\n"
                .str_replace(
                    "\n",
                    "\n\t",
                    "\n$stderr"
                )
                ."\n\nstdout:\n----------\n"
                .str_replace(
                    "\n",
                    "\n\t",
                    "\n$stdout"
                )
                ."\n\nCommands:\n----------\n"
                .str_replace(
                    "\n",
                    "\n\t",
                    "\n$bashCmds"
                )."\n\n"
            );
        }
        $seconds = round(microtime(true) - $startTime, 2);
        fwrite(STDERR, "\n\t\t#Completed in $seconds seconds\n");
    }

    protected function generateEntity(string $entityFqn)
    {
        $namespace   = self::TEST_PROJECT_ROOT_NAMESPACE;
        $doctrineCmd = <<<DOCTRINE
 dsm:generate:entity \
    --project-root-namespace="{$namespace}" \
    --entity-fully-qualified-name="{$entityFqn}"
DOCTRINE;
        $this->execDoctrine($doctrineCmd);
    }

    protected function generateUuidEntity(string $entityFqn)
    {
        $namespace   = self::TEST_PROJECT_ROOT_NAMESPACE;
        $doctrineCmd = <<<DOCTRINE
 dsm:generate:entity \
    --project-root-namespace="{$namespace}" \
    --entity-fully-qualified-name="{$entityFqn}" \
    --uuid-primary-key
DOCTRINE;
        $this->execDoctrine($doctrineCmd);
    }

    protected function generateField(string $propertyName, string $type)
    {
        $namespace   = self::TEST_PROJECT_ROOT_NAMESPACE;
        $doctrineCmd = <<<DOCTRINE
 dsm:generate:field \
    --project-root-path="{$this->workDir}" \
    --project-root-namespace="{$namespace}" \
    --field-property-name="{$propertyName}" \
    --field-property-doctrine-type="{$type}"
DOCTRINE;
        $this->execDoctrine($doctrineCmd);
    }

    protected function setField(string $entityFqn, string $fieldFqn)
    {
        $namespace   = self::TEST_PROJECT_ROOT_NAMESPACE;
        $doctrineCmd = <<<DOCTRINE
 dsm:set:field \
    --project-root-path="{$this->workDir}" \
    --project-root-namespace="{$namespace}" \
    --entity="{$entityFqn}" \
    --field="{$fieldFqn}"
DOCTRINE;
        $this->execDoctrine($doctrineCmd);
    }

    protected function execDoctrine(string $doctrineCmds)
    {
        $phpCmd  = $this->isTravis() ? 'php' : 'phpNoXdebug';
        $bash    = <<<BASH
$phpCmd bin/doctrine $doctrineCmds    
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

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @throws \Exception
     */
    public function testRunTests()
    {
        $this->assertWeCheckAllPossibleRelationTypes();
        if (isset($_SERVER[Constants::QA_QUICK_TESTS_KEY])
            && (int)$_SERVER[Constants::QA_QUICK_TESTS_KEY] === Constants::QA_QUICK_TESTS_ENABLED
        ) {
            $this->markTestSkipped('Quick tests is enabled');
        }
        /** @lang bash */
        $bashCmds = <<<BASH

set +x
echo "

--------------------------------------------------
STARTS Running Tests In {$this->workDir}
--------------------------------------------------

"

bin/qa

echo "

--------------------------------------------------
DONE Running Tests In {$this->workDir}
--------------------------------------------------

"
set -x
BASH;
        $this->execBash($bashCmds);
    }

    /**
     * Generate all test entities
     *
     * @return array
     */
    protected function generateEntities(): array
    {
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $this->generateEntity($entityFqn);
        }

        return self::TEST_ENTITIES;
    }

    protected function generateStandardFieldEntities()
    {
        $generatedEntities = [];
        foreach (FieldGenerator::STANDARD_FIELDS as $field) {
            $entityFqn = self::TEST_ENTITY_NAMESPACE_BASE . '\\' .$field;
            $this->generateUuidEntity($entityFqn);
        }

        return $generatedEntities;
    }

    /**
     * Generate all test relations
     *
     * @return void
     */
    protected function generateRelations(): void
    {
        foreach (self::TEST_RELATIONS as $relation) {
            $this->setRelation(...$relation);
        }
    }

    /**
     * Generate one field per common type
     *
     * @return void
     */
    protected function generateFields(): void
    {
        foreach (MappingHelper::COMMON_TYPES as $type) {
            $this->generateField($type, $type);
        }
    }

    /**
     * Set each field type on each entity type
     *
     * @param array $entities
     * @param array $fields
     * @return void
     */
    protected function setFields(array $entities, array $fields): void
    {
        foreach ($entities as $entityFqn) {
            foreach ($fields as $fieldFqn) {
                $this->setField($entityFqn, $fieldFqn);
            }
        }
    }

    /**
     * @param array $types
     * @return array
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getFieldFqns(array $types)
    {
        $fieldNamespace = self::TEST_FIELD_NAMESPACE_BASE . '\\Traits\\';

        $fieldFqns = [];
        foreach ($types as $type) {
            if (strpos($type, 'FieldTrait') === false) {
                $fieldFqns[] = $fieldNamespace . Inflector::classify($type) . 'FieldTrait';
                continue;
            }

            $fieldFqns[] = $fieldNamespace . $type;
        }

        return $fieldFqns;
    }
}
