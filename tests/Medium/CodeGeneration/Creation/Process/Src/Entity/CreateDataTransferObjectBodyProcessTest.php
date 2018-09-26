<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Creation\Process\Src\Entity;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\CreateDataTransferObjectBodyProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\CreateDataTransferObjectBodyProcess
 * @medium
 */
class CreateDataTransferObjectBodyProcessTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/CreateDataTransferObjectBodyProcessTest';

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
    }

    /**
     * @test
     */
    public function itCanCreateTheDtoBodyForAnEntityWithFields()
    {
        $file = new File(
            __DIR__ . '/../../../../../../../codeTemplates/src/Entity/DataTransferObjects/TemplateEntityDto.php'
        );
        $file->loadContents();
        $entityFqn = self::TEST_ENTITIES_ROOT_NAMESPACE . '\\Company\\Director';
        $this->getProcess()
             ->setEntityFqn($entityFqn)
             ->run(new File\FindReplace($file));
        $expected = '<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\DataTransferObjects;

/**
 * This data transfer object should be used to hold unvalidated update data,
 * ready to be fed into the Entity::update method
 *
 * This class should never have any logic beyond getters and setters
 */
class TemplateEntityDto
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
    }

	public function setDatetime(?\DateTime $datetime): self {
        $this->datetime=$datetime;
    }

	public function setFloat(?float $float): self {
        $this->float=$float;
    }

	public function setDecimal($decimal): self {
        $this->decimal=$decimal;
    }

	public function setInteger(?int $integer): self {
        $this->integer=$integer;
    }

	public function setText(?string $text): self {
        $this->text=$text;
    }

	public function setBoolean(?bool $boolean): self {
        $this->boolean=$boolean;
    }

	public function setJson(?string $json): self {
        $this->json=$json;
    }

}';
        $actual   = $file->getContents();
        self::assertNotEmpty($actual);
        self::assertSame($expected, $actual);

    }

    private function getProcess(): CreateDataTransferObjectBodyProcess
    {
        return new CreateDataTransferObjectBodyProcess(new ReflectionHelper(new NamespaceHelper()));
    }
}