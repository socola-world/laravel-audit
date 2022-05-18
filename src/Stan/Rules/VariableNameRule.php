<?php

namespace SocolaDaiCa\LaravelAudit\Stan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

class VariableNameRule implements Rule
{
    public function getNodeType(): string
    {
//        return Variable::class;
        return InClassMethodNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        return [];
//        if ($node instanceof Variable == false) {
//            return [];
//        }
        /* @var Variable $node */

        file_put_contents('variable.txt', $node->name."\n", FILE_APPEND);

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
