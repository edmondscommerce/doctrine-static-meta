<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class InjectTraitIntoClass
{
    private Statements $statements;
    private string     $traitShortName;

    public function __construct(private string $origClassCode, private string $traitFqn)
    {
        $this->statements     = new Statements($this->origClassCode);
        $this->traitShortName = substr(strrchr($this->traitFqn, "\\"), 1);
    }

    public function getModifiedCode(): string
    {
        $this->run();

        return $this->statements->getCode();
    }

    /** @return array<string,string> */
    private function getTraitUsed(): array
    {
        $nodeFinder = new NodeFinder();
        /** @var Stmt\TraitUse[] $nodes */
        $nodes     = $nodeFinder->findInstanceOf($this->statements->getStmts(), Node\Stmt\TraitUse::class);
        $traitFqns = [];
        foreach ($nodes as $node) {
            foreach ($node->traits as $trait) {
                $short = end($trait->parts);
                if (!\is_string($short)) {
                    throw new \RuntimeException('Got unexpected short name: ' . $short);
                }
                if (isset($traitFqns[$short])) {
                    throw new \RuntimeException('Got unexpected short name already used for trait');
                }
                $traitFqns[$short] = implode('/', $trait->parts);
            }
        }

        return $traitFqns;
    }

    /**
     * @throws ModificationException
     */
    private function assertNotAlreadyUsed(): void
    {
        $alreadyUsed = $this->getTraitUsed();

        if (isset($alreadyUsed[$this->traitShortName])) {
            throw new ModificationException(
                'Trait with short name ' . $this->traitShortName .
                ' is already used: ' . print_r($alreadyUsed, true)
            );
        }

        if (\in_array($this->traitFqn, $alreadyUsed, true)) {
            throw new ModificationException(
                'Trait with FQN ' . $this->traitFqn .
                ' is already used: ' . print_r($alreadyUsed, true)
            );
        }
    }

    private function addTrait(): void
    {
        //build a visitor that will add the trait statement
        $visitor = new class($this->traitFqn, $this->traitShortName) extends NodeVisitorAbstract {
            private bool $haveAddedUseStmt = false;

            public function __construct(private string $traitFqn, private string $traitShortName)
            {
            }

            /**
             * The leave node method is called as we enter leave node
             */
            public function leaveNode(Node $node): int|array|null
            {
                if ($node instanceof Stmt\Use_) {
                    if ($this->haveAddedUseStmt) {
                        return null;
                    }
                    $this->haveAddedUseStmt = true;

                    return [
                        $node,
                        new Stmt\Use_([new Stmt\UseUse(new Node\Name($this->traitFqn))]),
                    ];
                }
                if ($node instanceof Stmt\Class_) {
                    // add the new trait use statement
                    $node->stmts[] = new Stmt\TraitUse([new Node\Name($this->traitShortName)]);

                    // and now stop traversing, we're done
                    return NodeTraverser::STOP_TRAVERSAL;
                }

                return null;
            }
        };
        //now build a traverser, add this visitor and run the traverse
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->statements->getStmts());

    }

    /**
     * @throws ModificationException
     */
    private function run(): void
    {
        $this->assertNotAlreadyUsed();
        $this->addTrait();
    }
}