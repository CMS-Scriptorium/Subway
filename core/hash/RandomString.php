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

namespace Subway\core\hash;

class RandomString
{

    /**
     *  Function to generate a random-string.
     *  Random-Strings are often used inside LEPTON-CMS, e.g. Captcha, Modul-Form, User-Account, Forgot-Password, etc.
     *
     *  @param	int     $iNumOfChars    Number of chars to generate. Default is 8 (chars).
     *
     *  @param	string  $aType          Type, default is 'alphanum'.
     *                  Possible values are:
     *                  'alphanum'      = Generates an alphanumeric string with chars and numbers.
     *                  'alpha'         = Generates only chars.
     *                  'chars'         = Also generates only chars.
     *                  'hex'           = Hexadecimal.
     *                  'hex-lower'     = Hexadecimal in lower cases.
     *                  'lower'         = Only lower cases are used.
     *                  'num'           = Generates only numbers.
     *                  'pass'          = Generates an alphanumeric string within some special chars e.g. '&', '$' or '|'.
     *                  '<anyString>'   = Generates a shuffled string with these chars.
     *
     *
     *  @return string  A shuffled string within the chars.
     *
     *  @examples       RandomString::generate()
     *                      - Will generate something like 'abC2puwm' (8 chars).
     *
     *                  RandomString::generate(5)
     *                      - The same, but only 5 chars, e.g. 'abc56' or '2wsd4'.
     *
     *                  RandomString::generate(8, 'num')
     *                      - Will result in a random number-string, e.g. '0898124'
     *
     *                  RandomString::generate(8, 'Aldus')
     *                      - Will generate a shuffled-string with theese chars, e.g: 'sAdludsA'.
     *
     *                  RandomString::generate(8, 'hex-lower')
     *                      - Will generate a shuffled-string with hex-decimalchars like 'afb2c22e7'.
     *
     */
    static public function generate(int $iNumOfChars = 8, string $aType = "alphanum"): string
    {
        switch (strtolower($aType))
        {
            case 'alphanum':
                $salt = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                break;

            case 'alpha':
            case 'chars':
                $salt = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;

            case 'hex':
                $salt = 'ABCDEF0123456789';
                break;

            case 'hex-lower':
                $salt = 'abcdef0123456789';
                break;

            case 'lower':
                $salt = 'abcefghijklmnopqrstuvwxyz';
                break;

            case 'num':
                $salt = '1234567890';
                break;

            case 'pass':
                $salt = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-.";
                break;

            default:
                $salt = (is_array($aType)) ? implode("", $aType) : (string) $aType;
                break;
        }

        $max = strlen($salt);
        if ($iNumOfChars > $max)
        {
            do
            {
                $salt .= $salt;
            } while (strlen($salt) < $iNumOfChars);
        }

        return substr(str_shuffle($salt), 0, $iNumOfChars);
    }
}
