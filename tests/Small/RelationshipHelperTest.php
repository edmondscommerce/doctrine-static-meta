<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use EdmondsCommerce\DoctrineStaticMeta\RelationshipHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @small
 * @testdox EdmondsCommerce\DoctrineStaticMeta\RelationshipHelper
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\RelationshipHelper
 */
class RelationshipHelperTest extends TestCase
{

    /**
     * @param array $mapping
     * @param bool  $expectedResult
     * @param bool  $expectException
     *
     * @test
     * @dataProvider isPluralData
     */
    public function itCanDetermineIfPlural(array $mapping, bool $expectedResult, bool $expectException): void
    {
        $class        = $this->getTestClass();
        if ($expectException === true) {
            $this->expectException(InvalidArgumentException::class);
        }
        $actualResult = $class->isPlural($mapping);
        self::assertSame($expectedResult, $actualResult);
    }

    /**
     * @param array  $mapping
     * @param string $expectedMethod
     * @param bool   $expectException
     *
     * @test
     * @dataProvider adderData
     */
    public function itCanGetTheAdders(array $mapping, string $expectedMethod, bool $expectException): void
    {
        $class = $this->getTestClass();
        if ($expectException === true) {
            $this->expectException(InvalidArgumentException::class);
        }
        $actualMethod = $class->getAdderFromDoctrineMapping($mapping);
        self::assertSame($expectedMethod, $actualMethod);
    }

    /**
     * @param array  $mapping
     * @param string $expectedMethod
     *
     * @test
     * @dataProvider getterData
     */
    public function itCanGetTheGetters(array $mapping, string $expectedMethod): void
    {
        $class        = $this->getTestClass();
        $actualMethod = $class->getGetterFromDoctrineMapping($mapping);
        self::assertSame($expectedMethod, $actualMethod);
    }

    /**
     * @param array  $mapping
     * @param string $expectedMethod
     * @param bool   $expectException
     *
     * @test
     * @dataProvider removerData
     */
    public function itCanGetTheRemovers(array $mapping, string $expectedMethod, bool $expectException): void
    {
        $class = $this->getTestClass();
        if ($expectException === true) {
            $this->expectException(InvalidArgumentException::class);
        }
        $actualMethod = $class->getRemoverFromDoctrineMapping($mapping);
        self::assertSame($expectedMethod, $actualMethod);
    }

    /**
     * @param array  $mapping
     * @param string $expectedMethod
     *
     * @test
     * @dataProvider setterData
     */
    public function itCanGetTheSetters(array $mapping, string $expectedMethod): void
    {
        $class        = $this->getTestClass();
        $actualMethod = $class->getSetterFromDoctrineMapping($mapping);
        self::assertSame($expectedMethod, $actualMethod);
    }

    public function getterData(): array
    {
        return [
            [$this->getMappingArray('user', ClassMetadataInfo::ONE_TO_ONE), 'getUser'],
            [$this->getMappingArray('user', ClassMetadataInfo::MANY_TO_ONE), 'getUser'],
            [$this->getMappingArray('users', ClassMetadataInfo::ONE_TO_MANY), 'getUsers'],
            [$this->getMappingArray('users', ClassMetadataInfo::MANY_TO_MANY), 'getUsers'],
        ];
    }

    public function setterData(): array
    {
        return [
            [$this->getMappingArray('user', ClassMetadataInfo::ONE_TO_ONE), 'setUser'],
            [$this->getMappingArray('user', ClassMetadataInfo::MANY_TO_ONE), 'setUser'],
            [$this->getMappingArray('users', ClassMetadataInfo::ONE_TO_MANY), 'setUsers'],
            [$this->getMappingArray('users', ClassMetadataInfo::MANY_TO_MANY), 'setUsers'],
        ];
    }

    public function adderData(): array
    {
        return [
            [$this->getMappingArray('user', ClassMetadataInfo::MANY_TO_ONE), 'setUser', true],
            [$this->getMappingArray('users', ClassMetadataInfo::ONE_TO_MANY), 'addUser', false],
            [$this->getMappingArray('users', ClassMetadataInfo::MANY_TO_MANY), 'addUser', false],
        ];
    }

    public function removerData(): array
    {
        return [
            [$this->getMappingArray('user', ClassMetadataInfo::MANY_TO_ONE), 'setUser', true],
            [$this->getMappingArray('users', ClassMetadataInfo::ONE_TO_MANY), 'removeUser', false],
            [$this->getMappingArray('users', ClassMetadataInfo::MANY_TO_MANY), 'removeUser', false],
        ];
    }

    public function isPluralData(): array
    {
        return [
            [$this->getMappingArray('user', ClassMetadataInfo::ONE_TO_ONE), false, false],
            [$this->getMappingArray('user', ClassMetadataInfo::MANY_TO_ONE), false, false],
            [$this->getMappingArray('users', ClassMetadataInfo::ONE_TO_MANY), true, false],
            [$this->getMappingArray('users', ClassMetadataInfo::MANY_TO_MANY), true, false],
            [$this->getMappingArray('users', 100), true, true],
        ];
    }

    private function getMappingArray(string $property, int $type): array
    {
        return [
            'fieldName' => $property,
            'type'      => $type,
        ];
    }

    private function getTestClass(): RelationshipHelper
    {
        return new RelationshipHelper();
    }
}
