<?php

namespace SocolaDaiCa\LaravelAudit\Stan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassMethodsNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

class ClassMethodNameRule implements Rule
{
    public function getNodeType(): string
    {
        return ClassMethodsNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        return [];

        if ($node instanceof ClassMethodsNode == false) {
            return [];
        }
        /* @var ClassMethodsNode $node */

        file_put_contents('variable.txt', 'aa'."\n", FILE_APPEND);

//        dump('123');
//        die('123');
//        var_dump(get_class($node));
        return [
            RuleErrorBuilder::message(
                'New Person instance can be created only in PersonFactory.'.$node->name
            )->build(),
        ];
//        dd('123');
//        die();
        // TODO: Implement processNode() method.
    }
}
