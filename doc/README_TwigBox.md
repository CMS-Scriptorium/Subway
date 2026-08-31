## TwigBox
***
![Attention please still in progress](../img/construction_site.png) 

##### Namespace
Subway\core\template  

##### Example given
```php
use Subway\core\template\TwigBox;

$oTWIG = TwigBox::getInstance();
$oTWIG->registerModule("Subway", "SubwayNameSpace");

echo $oTWIG->render(
    "@SubwayNameSpace/output.twig",
    [
        'Message' => "whatever example"
    ]
);
```
