<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification;

use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

/**
 * This class handles build the statements array from a set of code and then generating the updated code including any
 * modifications to the statements
 */
class Statements
{
    /** @var \PhpParser\Node\Stmt[] */
    private ?array $oldStmts;
    /** @var  mixed[] */
    private array $oldTokens;
    /** @var \PhpParser\Node[] */
    private array $stmts;

    public function __construct(private string $code)
    {
        $lexer     = new Emulative([
                                       'usedAttributes' => [
                                           'comments',
                                           'startLine',
                                           'endLine',
                                           'startTokenPos',
                                           'endTokenPos',
                                       ],
                                   ]);
        $parser    = (new ParserFactory())->create(ParserFactory::ONLY_PHP7, $lexer);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new CloningVisitor());

        $this->oldStmts  = $parser->parse($this->code);
        $this->oldTokens = $lexer->getTokens();
        $this->stmts     = $traverser->traverse($this->oldStmts);
    }

    /**
     * Generate updated code, preserving formatting as much as possible for any unchanged code
     */
    public function getCode(array $stmts = null): string
    {
        $stmts = $stmts ?? $this->stmts;

        return (new Standard())->printFormatPreserving($stmts, $this->oldStmts, $this->oldTokens);
    }

    /**
     * Get the array of statements ready for traversing and modification
     */
    public function getStmts(): array
    {
        return $this->stmts;
    }
}