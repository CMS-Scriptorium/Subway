# RandomString Class

A utility class for generating random strings with various character set options. Perfect for creating secure tokens, captcha codes, temporary passwords, and other randomized strings commonly used throughout web applications.

## Overview

`RandomString` is a static utility class that provides flexible random string generation with multiple predefined character sets and the ability to define custom character pools.

**Package:** `Subway\core\hash`  
**Version:** 0.1.1  
**Minimum PHP:** 8.3 (requires 8.4.x)  
**License:** CC BY-SA 4.0

## Installation

Include the class in your namespace:

```php
use Subway\core\hash\RandomString;
```

## Method

### `generate(int $iNumOfChars = 8, string $aType = "alphanum"): string`

Generates a random string with the specified length and character set.

#### Parameters

- **`$iNumOfChars`** (int, default: `8`)  
  The number of characters to generate in the output string.

- **`$aType`** (string, default: `"alphanum"`)  
  The type of characters to use. Accepts predefined types or a custom character string.

#### Return Value

Returns a shuffled string of the specified length containing characters from the selected character set.

## Character Set Types

### Predefined Types

| Type | Characters | Example Output |
|------|-----------|-----------------|
| `alphanum` | `a-z`, `A-Z`, `0-9` | `abC2puwm` |
| `alpha` | `a-z`, `A-Z` | `aBcDeFgH` |
| `chars` | `a-z`, `A-Z` (alias for `alpha`) | `XyZaBcDe` |
| `hex` | `0-9`, `A-F` (hexadecimal uppercase) | `A1B2C3D4` |
| `hex-lower` | `0-9`, `a-f` (hexadecimal lowercase) | `a1b2c3d4` |
| `lower` | `a-z` only | `abcdefgh` |
| `num` | `0-9` only | `0898124` |
| `pass` | `a-z`, `A-Z`, `0-9`, `_`, `-`, `.` (password-safe) | `aB3_c-De.` |

### Custom Character Sets

Pass any custom string or array of characters:

```php
// Custom string
RandomString::generate(10, 'Aldus');  // Result: sAdludsAls

// Custom array
RandomString::generate(10, ['A', 'l', 'd', 'u', 's']);  // Result: dAldusAldA
```

## Usage Examples

### Basic Usage (Default Parameters)

```php
$token = RandomString::generate();
// Result: 'abC2puwm' (8 alphanumeric characters)
```

### Custom Length

```php
$shortCode = RandomString::generate(5);
// Result: 'abc56' (5 alphanumeric characters)

$longToken = RandomString::generate(32);
// Result: 'aBcD1eFgH2iJkL3mNoPqR4sTuVwXyZ5ab' (32 characters)
```

### Numeric Codes

```php
$numericCode = RandomString::generate(8, 'num');
// Result: '0898124' (8 digits)
```

### Hexadecimal Values

```php
$hexColor = RandomString::generate(6, 'hex-lower');
// Result: 'afb2c2' (lowercase hex)

$hexToken = RandomString::generate(16, 'hex');
// Result: 'A1B2C3D4E5F6A7B8' (uppercase hex)
```

### Password-Safe Strings

```php
$password = RandomString::generate(16, 'pass');
// Result: 'aB3_c-De.fGh4iJk' (includes letters, numbers, and special chars)
```

### Alphabetic Only

```php
$captcha = RandomString::generate(6, 'alpha');
// Result: 'aBcDeF' (letters only)
```

### Custom Character Set

```php
$diceRoll = RandomString::generate(5, '123456');
// Result: '34251' (only digits 1-6)

$customToken = RandomString::generate(10, 'ACGT');
// Result: 'ATCGATCGAT' (DNA bases)
```

## Common Use Cases

### User Account Tokens

```php
$resetToken = RandomString::generate(32, 'alphanum');
```

### Captcha Code

```php
$captchaCode = RandomString::generate(6, 'alpha');
```

### Temporary Password

```php
$tempPassword = RandomString::generate(12, 'pass');
```

### Session ID

```php
$sessionId = RandomString::generate(32, 'hex-lower');
```

### API Key

```php
$apiKey = RandomString::generate(32, 'alphanum');
```

## Behavior Notes

### Length Exceeding Salt Size

If the requested number of characters exceeds the size of the character set, the salt is automatically repeated:

```php
$result = RandomString::generate(100, 'alpha');
// Generates 100 letters (salt repeated internally)
```

### Case Insensitivity

The `$aType` parameter is case-insensitive:

```php
RandomString::generate(10, 'ALPHANUM');  // Works
RandomString::generate(10, 'alphanum');  // Works
RandomString::generate(10, 'AlphaNum');  // Works
```

### Randomness

Each call produces a new random string due to `str_shuffle()`. Multiple calls will produce different results:

```php
$token1 = RandomString::generate();
$token2 = RandomString::generate();
// $token1 !== $token2
```

## Known Issues

⚠️ **Note:** The character set for `alphanum` and `alpha` types is missing the letter 'd':
- `alphanum` salt: `abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890`
- `alpha` salt: `abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ`

This appears to be intentional but may warrant review.

## Performance Considerations

- The method uses `str_shuffle()`, which is suitable for most use cases
- For cryptographic security requirements, consider using `random_bytes()` and `bin2hex()` instead
- Generation is fast even for large strings (1000+ characters)

## Security Notes

This class is suitable for:
- Non-security-critical random strings (CAPTCHA codes, form tokens)
- Session identifiers with other security measures
- Randomized UI elements

This class is **NOT** suitable for:
- Cryptographic keys
- Security tokens without additional entropy
- Password generation (use dedicated password generation libraries)

For cryptographically secure random generation, use:

```php
$randomBytes = random_bytes(16);
$token = bin2hex($randomBytes);
```

## Version History

| Version | Changes |
|---------|---------|
| 0.1.1 | Initial release |

## License

Creative Commons Attribution-ShareAlike 4.0 International (CC BY-SA 4.0)  
See: https://creativecommons.org/licenses/by-sa/4.0/

## Author

**Kant (Aldus)**

## Related

- [Subway Project](https://github.com/CMS-Scriptorium/Subway)
- WBCE CMS 1.6.x Compatibility
