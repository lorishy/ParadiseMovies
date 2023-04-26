<?php
// src/Doctrine/CurrentTimestampFunction.php
namespace App\Doctrine;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class CurrentTimestampFunction extends FunctionNode
{
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'CURRENT_TIMESTAMP()';
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}