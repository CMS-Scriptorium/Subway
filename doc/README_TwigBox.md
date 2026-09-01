# TwigBox

## Overview

**TwigBox** is a singleton class that serves as the central wrapper and manager for the Twig templating engine within the Subway project for WBCE. It initializes, configures, and manages the Twig environment, including template paths, filters, functions, and extensions.

## Location

`core/template/TwigBox.php`

## Purpose

TwigBox provides a unified interface for:
- Initializing and configuring the Twig templating engine
- Registering template directories and namespaces
- Managing Twig extensions, filters, and custom functions
- Rendering templates with parsed data

## Key Features

### Singleton Pattern
Uses the `Singleton` trait to ensure only one instance of TwigBox exists throughout the application lifecycle.

### Template Path Management
- **Theme templates**: Registers theme-specific template directories
- **Frontend templates**: Registers frontend template directories
- **Module templates**: Handles module-specific template directories with support for backend and frontend variants

### Twig Extensions & Customizations

#### Built-in Extensions
- **DebugExtension**: Enables debug functionality in Twig templates
- **TwigOperators** (Twig 3.21.0+): Custom operators for template logic
- **TwigOperatorsOld**: Fallback operators for older Twig versions

#### Custom Functions
- `fileExists()`: Check if a file exists in templates
- `processTranslationL()`: Process and translate text strings
- `formatWithMYSQL()`: Format data according to MySQL standards
- `insertCssFile()`: Dynamically insert CSS files
- `insertJsFile()`: Dynamically insert JavaScript files

#### Custom Filters
- **getFilterDisplay()**: Control display properties
- **getFilterIntersects()**: Check for intersecting values

### Global Variables
The following global constants and variables are automatically available in all templates:
- `WB_PATH`: Path to WebsiteBaker/WBCE installation
- `WB_URL`: URL to WebsiteBaker/WBCE installation
- `ADMIN_URL`: Admin panel URL
- `THEME_PATH`: Current theme path
- `THEME_URL`: Current theme URL
- `MEDIA_DIRECTORY`: Media storage directory
- `LANGUAGE`: Current language setting
- `TEXT`: Global text/message translations

## Class Structure

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `$loader` | `?object` | Twig FilesystemLoader instance |
| `$parser` | `?object` | Twig Environment instance |
| `$instance` | `static` | Singleton instance holder |

### Constants

| Constant | Value | Purpose |
|----------|-------|---------|
| `TWIG_BASE_PATH` | `/include/Sensio/` | Path to Twig library files |
| `TEMPLATE_DIR` | `/templates/` | Standard templates directory |

## Public Methods

### `registerPath(string $sPath = "", string $sNamespace = "__main__"): bool`

Registers a new template directory path with an optional namespace.

**Parameters:**
- `$sPath` (string): Absolute path to a template directory
- `$sNamespace` (string): Namespace identifier for the path (default: `"__main__"`)

**Returns:** `bool` - `true` if path was successfully registered, `false` if path is empty or doesn't exist

**Example:**
```php
$twig = TwigBox::getInstance();
$twig->registerPath("/path/to/templates", "my_module");