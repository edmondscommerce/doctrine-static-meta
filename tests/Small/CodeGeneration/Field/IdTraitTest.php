<?php declare(strict_types=1);
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\IdTrait;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Class IdTraitTest
 *
 * @small
 * @testdox EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\IdTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\IdTrait
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Field
 */
class IdTraitTest extends TestCase
{

    private $fakeFindAndReplace;

    /**
     * @test
     *      *      */
    public function itWillDefaultToUsingTheUuidTrait(): void
    {
        $class = $this->getClass();
        $class->updateEntity('/not/a/real/file');
        $expectedTrait = 'use DSM\Fields\Traits\PrimaryKey\UuidFieldTrait;';
        $actualTrait   = $this->fakeFindAndReplace->getTraitThatWasReplaced();
        $this->assertSame($expectedTrait, $actualTrait);
    }

    /**
     * @param int    $type
     * @param string $expectedTrait
     *
     * @test
     *      *      * @dataProvider getDifferentIdTraits
     */
    public function itCanUpdateTheTraitsToDifferentOnes(int $type, string $expectedTrait): void
    {
        $class = $this->getClass();
        $class->setIdTrait($type);
        $class->updateEntity('/not/a/real/file');
        $actualTrait = $this->fakeFindAndReplace->getTraitThatWasReplaced();
        $this->assertSame($expectedTrait, $actualTrait);
    }

    public function getDifferentIdTraits(): array
    {
        return [
            [IdTrait::INTEGER_ID_FIELD_TRAIT, 'use DSM\Fields\Traits\PrimaryKey\IntegerIdFieldTrait;'],
            [IdTrait::NON_BINARY_UUID_TRAIT, 'use DSM\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait;'],
            [IdTrait::UUID_FIELD_TRAIT, 'use DSM\Fields\Traits\PrimaryKey\UuidFieldTrait;'],
        ];
    }

    /**
     *      * @test
     */
    public function itWillNotCallTheFindAndReplaceWhenItDoesNotNeedTo(): void
    {
        $class = $this->getClass();
        $class->setIdTrait(IdTrait::ID_FIELD_TRAIT);
        $class->updateEntity('/not/a/real/file');
        $this->assertNull($this->fakeFindAndReplace->getTraitThatWasReplaced());
    }

    /**
     *      * @test
     */
    public function itWillThrowAnExceptionIfGivenAnInvalidType(): void
    {
        $class = $this->getClass();
        $this->expectException(LogicException::class);
        $class->setIdTrait(-1);
    }

    private function getClass(): IdTrait
    {
        $findReplace = $this->getFakeFindAndReplace();

        return new IdTrait($findReplace);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function getFakeFindAndReplace()
    {
        if ($this->fakeFindAndReplace === null) {
            $this->fakeFindAndReplace = new class(new NamespaceHelper()) extends FindAndReplaceHelper
            {

                private $traitThatWasReplaced;

                public function findReplace(
                    string $find,
                    string $replace,
                    string $filePath
                ): FindAndReplaceHelper {
                    $this->traitThatWasReplaced = $replace;

                    return $this;
                }

                public function getTraitThatWasReplaced(): ?string
                {
                    return $this->traitThatWasReplaced;
                }
            };
        }

        return $this->fakeFindAndReplace;
    }
}
