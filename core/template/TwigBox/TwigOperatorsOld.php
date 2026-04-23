<?php

/**
 * @package         Subway
 * @version         0.1.0
 * @authors         Kant (Aldus)
 * @license         CC BY-SA 4.0
 * @license_terms   https://creativecommons.org/licenses/by-sa/4.0/
 * @platform        WBCE 1.6.x
 * @requirements    PHP 8.4.x (8.3 recommented)
 */

namespace Subway\core\template\TwigBox;

class TwigOperatorsOld extends \Twig\Extension\AbstractExtension
{
    // initialize
    public function __construct()
    {
    
    }
    
    /**
     *  See: page 40 ff. inside the twig documentation-pdf. 
     *      https://twig.symfony.com/doc/2.x/
     *      https://twig.symfony.com/doc/2.x/advanced.html#operators
     */
    public function getOperators()
    {
        return array(
            array(
                '!' => array(
                    'precedence' => 50,
                    'class' => 'Twig\Node\Expression\Unary\NotUnary'
                ),
                
                '¬' => array(
                    'precedence' => 50,
                    'class' => 'Twig\Node\Expression\Unary\NotUnary'
                )
             ),
            array(
                '||' => array(
                    'precedence' => 10,
                    'class' => 'Twig\Node\Expression\Binary\OrBinary',
                    'associativity' => \Twig\ExpressionParser::OPERATOR_LEFT
                ),
                '&&' => array(
                    'precedence' => 15,
                    'class' => 'Twig\Node\Expression\Binary\AndBinary',
                    'associativity' => \Twig\ExpressionParser::OPERATOR_LEFT
                )
            )
        );
    }
}