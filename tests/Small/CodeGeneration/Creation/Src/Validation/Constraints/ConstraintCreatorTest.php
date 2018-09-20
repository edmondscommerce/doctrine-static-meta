<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Validation\Constraints;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\ConstraintCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\ConstraintCreator
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator
 */
class ConstraintCreatorTest extends TestCase
{
    /**
     * @test
     * @small
     */
    public function itCanCreateANewFileObjectWithTheCorrectContent()
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Validation\\Constraints\\IsBlueConstraint';
        $file         = $this->getConstraintCreator()
                             ->createTargetFileObject($newObjectFqn)
                             ->getTargetFile();
        $expected     = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * This is a template constraint. The Constraint does very little other than specify a message and imply a
 * ConstraintValidator with the same FQN as this class, but with `Validator` appended.
 *
 * @see https://symfony.com/doc/2.6/cookbook/validation/custom_constraint.html
 *
 * Suggest using the `./bin/doctrine dsm:override:create` command to create a new
 * override straight after you generate the file.
 *
 * Then you can edit it and update your override as required using
 * `./bin/doctrine dsm:overrides:update -a fromProject`
 *
 * And then reapply after a new build using
 * `./bin/doctrine dsm:overrides:update -a toProject`
 *
 */
class IsBlueConstraint extends Constraint
{
    public const VALUE_TYPE = \'(template value type)\';

    public const MESSAGE = \'The value %s is not a valid \' . self::VALUE_TYPE;

    public $message = self::MESSAGE;
}';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getConstraintCreator(): ConstraintCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new ConstraintCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
    }
}
