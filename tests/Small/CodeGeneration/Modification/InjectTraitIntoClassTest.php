<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Modification;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification\InjectTraitIntoClass;
use PHPUnit\Framework\TestCase;

/**
 * @covers InjectTraitIntoClass
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification\Statements
 */
class InjectTraitIntoClassTest extends TestCase
{
    private const CODE = <<<'PHP'
<?php

declare(strict_types=1);

namespace a\b\c;

use a\b\c\d\foo;
use x\y\z\bar;

class Baz{
    private int $baz=1;
    use foo;
    use bar;
}
PHP;

    private const EXPECTED = <<<'PHP'
<?php

declare(strict_types=1);

namespace a\b\c;

class Baz{
    private int $baz=1;
    use foo;
    use bar;
    use added;
}
PHP;


    /** @test */
    public function itCanAddATraitIntoAClass(): void
    {
        $class = new InjectTraitIntoClass(self::CODE, 'a\b\c\added');
        $class->run();
        $actual = $class->getModifiedCode();
        self::assertSame(self::EXPECTED, $actual);
    }

    /** @test */
    public function itWontAddDuplicateTrait(): void
    {
        $class = new InjectTraitIntoClass(self::CODE, 'a\b\c\foo');
        $class->run();
        $actual = $class->getModifiedCode();
        self::assertSame(self::CODE, $actual);
    }
}