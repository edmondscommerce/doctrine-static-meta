<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Creation\Src\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DataTransferObjectCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DataTransferObjectCreator
 * @medium
 */
class DataTransferObjectCreatorTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/DataTransferObjectCreatorTest';

    private const DTO = '<?php declare(strict_types=1);

namespace My\Test\Project\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;

/**
 * This data transfer object should be used to hold unvalidated update data,
 * ready to be fed into the Entity::update method
 *
 * This class should never have any logic beyond getters and setters
 */
final class PersonDto implements DataTransferObjectInterface
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var DateTime
     */
    private $datetime;

    /**
     * @var float
     */
    private $float;

    /**
     * @var 
     */
    private $decimal;

    /**
     * @var int
     */
    private $integer;

    /**
     * @var string
     */
    private $text;

    /**
     * @var bool
     */
    private $boolean;

    /**
     * @var string
     */
    private $json;

	public function getString(): ?string {
        return $this->string;
    }

	public function getDatetime(): ?\DateTime {
        return $this->datetime;
    }

	public function getFloat(): ?float {
        return $this->float;
    }

	public function getDecimal() {
        return $this->decimal;
    }

	public function getInteger(): ?int {
        return $this->integer;
    }

	public function getText(): ?string {
        return $this->text;
    }

	public function isBoolean(): ?bool {
        return $this->boolean;
    }

	public function getJson(): ?string {
        return $this->json;
    }

	public function setString(?string $string): self {
        $this->string=$string;
        return $this;
    }

	public function setDatetime(?\DateTime $datetime): self {
        $this->datetime=$datetime;
        return $this;
    }

	public function setFloat(?float $float): self {
        $this->float=$float;
        return $this;
    }

	public function setDecimal($decimal): self {
        $this->decimal=$decimal;
        return $this;
    }

	public function setInteger(?int $integer): self {
        $this->integer=$integer;
        return $this;
    }

	public function setText(?string $text): self {
        $this->text=$text;
        return $this;
    }

	public function setBoolean(?bool $boolean): self {
        $this->boolean=$boolean;
        return $this;
    }

	public function setJson(?string $json): self {
        $this->json=$json;
        return $this;
    }

}';

    private const NESTED_DTO = '<?php declare(strict_types=1);

namespace My\Test\Project\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;

/**
 * This data transfer object should be used to hold unvalidated update data,
 * ready to be fed into the Entity::update method
 *
 * This class should never have any logic beyond getters and setters
 */
final class ClientDto implements DataTransferObjectInterface
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var DateTime
     */
    private $datetime;

    /**
     * @var float
     */
    private $float;

    /**
     * @var 
     */
    private $decimal;

    /**
     * @var int
     */
    private $integer;

    /**
     * @var string
     */
    private $text;

    /**
     * @var bool
     */
    private $boolean;

    /**
     * @var string
     */
    private $json;

	public function getString(): ?string {
        return $this->string;
    }

	public function getDatetime(): ?\DateTime {
        return $this->datetime;
    }

	public function getFloat(): ?float {
        return $this->float;
    }

	public function getDecimal() {
        return $this->decimal;
    }

	public function getInteger(): ?int {
        return $this->integer;
    }

	public function getText(): ?string {
        return $this->text;
    }

	public function isBoolean(): ?bool {
        return $this->boolean;
    }

	public function getJson(): ?string {
        return $this->json;
    }

	public function setString(?string $string): self {
        $this->string=$string;
        return $this;
    }

	public function setDatetime(?\DateTime $datetime): self {
        $this->datetime=$datetime;
        return $this;
    }

	public function setFloat(?float $float): self {
        $this->float=$float;
        return $this;
    }

	public function setDecimal($decimal): self {
        $this->decimal=$decimal;
        return $this;
    }

	public function setInteger(?int $integer): self {
        $this->integer=$integer;
        return $this;
    }

	public function setText(?string $text): self {
        $this->text=$text;
        return $this;
    }

	public function setBoolean(?bool $boolean): self {
        $this->boolean=$boolean;
        return $this;
    }

	public function setJson(?string $json): self {
        $this->json=$json;
        return $this;
    }

}';

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
    }

    /**
     * @test
     */
    public function itCanCreateADto()
    {
        $newObjectFqn = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\DataTransferObjects\\PersonDto'
        );
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::DTO;
        $actual       = \str_replace(
            'namespace ' . $this->getCopiedFqn('My\\Test\\Project'),
            'namespace My\\Test\\Project\\',
            $file->getContents()
        );
        self::assertSame($expected, $actual);
    }

    private function getCreator(): DataTransferObjectCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        $creator = new DataTransferObjectCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory(),
            new ReflectionHelper($namespaceHelper)
        );
        $creator->setProjectRootNamespace($this->getCopiedFqn(self::TEST_PROJECT_ROOT_NAMESPACE));
        $creator->setProjectRootDirectory($this->copiedWorkDir);

        return $creator;
    }

    /**
     * @test
     */
    public function itCanCreateADtoFromAnEntityFqn()
    {
        $entityFqn = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Person'
        );
        $file      = $this->getCreator()
                          ->setNewObjectFqnFromEntityFqn($entityFqn)
                          ->createTargetFileObject()
                          ->getTargetFile();
        $expected  = self::DTO;
        $actual    = \str_replace(
            'namespace ' . $this->getCopiedFqn('My\\Test\\Project'),
            'namespace My\\Test\\Project\\',
            $file->getContents()
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANestedDto()
    {
        $newObjectFqn = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE .
            '\\Entity\\DataTransferObjects\\Another\\Deeply\\Nested\\ClientDto'
        );
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::NESTED_DTO;
        $actual       = \str_replace(
            'namespace ' . $this->getCopiedFqn('My\\Test\\Project'),
            'namespace My\\Test\\Project\\',
            $file->getContents()
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANestedDtoFromEntityFqn()
    {
        $entityFqn = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE .
            '\\Entities\\Another\\Deeply\\Nested\\Client'
        );
        $file      = $this->getCreator()
                          ->setNewObjectFqnFromEntityFqn($entityFqn)
                          ->createTargetFileObject()
                          ->getTargetFile();
        $expected  = self::NESTED_DTO;
        $actual    = \str_replace(
            'namespace ' . $this->getCopiedFqn('My\\Test\\Project'),
            'namespace My\\Test\\Project\\',
            $file->getContents()
        );
        self::assertSame($expected, $actual);
    }
}