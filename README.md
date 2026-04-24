# Subway
Nothing more and nothing less than a private study  
for additional code for [WBCE][1].
***

### Requirements
- PHP >= 8.4.1
- [WBCE][1] >= 1.6.4
- Twig >= 3.14.x

#### Examples
- code2
```php
use Subway\core\Pages;
use Subway\core\tools\Data;

echo "PageTree - Neu mit »Subway«.";

$aPages  = Pages::getInstance()->getPageTree(
	0, // root
  	['page_id', 'page_title', 'menu_title']
);

echo Data::display($aPages);
```

- loading Subway-frontend css
```php
\Subway\core\Subway::getInstance()->initFrontend();
```

[1]: https://wbce.org/de/wbce/
[2]: https://forum.wbce.org/search.php?action=show_recent
