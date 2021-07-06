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

    public function __construct(private string $origClassCode, private string $traitFqn)
    {
        $this->statements = new Statements($this->origClassCode);
    }

    public function getModifiedCode(): string
    {
        return $this->statements->getCode();
    }

    /** @return string[] */
    private function getTraitUsed(): array
    {
        $nodeFinder = new NodeFinder();
        /** @var Stmt\TraitUse[] $nodes */
        $nodes     = $nodeFinder->findInstanceOf($this->statements->getStmts(), Node\Stmt\TraitUse::class);
        $traitFqns = [];
        foreach ($nodes as $node) {
            foreach ($node->traits as $trait) {
                $traitFqns[] = implode('/', $trait->parts);
            }
        }

        return $traitFqns;
    }

    private function isAlreadyUsed(): bool
    {
        return in_array($this->traitFqn, $this->getTraitUsed(), true);
    }

    private function addTrait(): void
    {
        //build a visitor that will add the trait statement
        $visitor = new         class($this->traitFqn) extends NodeVisitorAbstract {
            private string $traitShortName;
            private bool   $haveAddedUseStmt = false;

            public function __construct(private string $traitFqn)
            {
                $this->traitShortName = substr(strrchr($this->traitFqn, "\\"), 1);
            }

            /**
             * The leave node method is called as we enter each node
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
                        new Stmt\Use_([new Stmt\UseUse(new Node\Name($this->traitShortName))]),
                    ];
                }
                if ($node instanceof Stmt\Class_) {
                    // add the new trait use statement
                    $node->stmts[] = new Stmt\TraitUse([new Node\Name($this->traitFqn)]);

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

    public function run(): void
    {
        if ($this->isAlreadyUsed()) {
            return;
        }
        $this->addTrait();
    }
}