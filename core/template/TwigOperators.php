<?php

declare(strict_types=1);

/**
 * @package         Subway
 * @version         0.1.0
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.4.x (8.3 recommented)
 */

namespace Subway\core\template;

use Twig\ExpressionParser\Infix\BinaryOperatorExpressionParser;
use Twig\ExpressionParser\Prefix\UnaryOperatorExpressionParser;
use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Node\Expression\Binary\AndBinary;
use Twig\Node\Expression\Binary\OrBinary;
use Twig\Node\Expression\Unary\NotUnary;

class TwigOperators extends AbstractExtension implements ExtensionInterface
{
    // initialize
    public function __construct()
    {
        // nothing here to do.
    }
    
    /**
     *  TWIG 3.21
     *  Method "getOperators" is deprecated!
     *
     *  See: https://twig.symfony.com/doc/3.x/deprecated.html
     *
     */
    #[\Override]
    public function getExpressionParsers(): array
    {
        return [
             new UnaryOperatorExpressionParser(NotUnary::class, '!', 50),
             new BinaryOperatorExpressionParser(OrBinary::class, '||', 10),
             new BinaryOperatorExpressionParser(AndBinary::class, '&&', 15)
        ];
    }
}
