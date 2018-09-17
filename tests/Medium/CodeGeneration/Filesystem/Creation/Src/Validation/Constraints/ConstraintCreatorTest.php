<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints\ConstraintCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints\ConstraintCreator
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\AbstractCreator
 */
class ConstraintCreatorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/ConstraintCreatorTest';

    /**
     * @test
     * @medium
     */
    public function itCanCreateANewFileObjectWithTheCorrectContent()
    {
        $newObjectFqn = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Validation\\Constraints\\IsBlueValidator';
        $template     = $this->getConstraintTemplate();
        $template->createTargetFileObject($newObjectFqn);
        $path = $template->write();
        self::assertFileExists($path);
        $expected = '<?php declare(strict_types=1);

namespace TemplateNamespace\Validation\Constraints;

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
class IsBlueValidator extends Constraint
{
    public const VALUE_TYPE = \'(template value type)\';

    public const MESSAGE = \'The value %s is not a valid \' . self::VALUE_TYPE;

    public $message = self::MESSAGE;
}';
        self::assertSame($expected, \ts\file_get_contents($path));
    }

    private function getConstraintTemplate(): ConstraintCreator
    {
        $namespaceHelper = new NamespaceHelper();

        $template =
            new ConstraintCreator(
                new FileFactory($namespaceHelper, new Config(ConfigTest::SERVER)),
                new FindReplaceFactory(),
                $namespaceHelper,
                new Writer()
            );
        $template->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE)
                 ->setProjectRootDirectory(self::WORK_DIR);

        return $template;

    }
}