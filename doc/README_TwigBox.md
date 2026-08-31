### Subway\ TwigBox
***
![Attention please still in progress](../img/construction_site.png) 

#####
- Makes use of ~include/Sensio/Twig  
- Singleton instance  
> use Subway\core\template\TwigBox;  
> $oTwig = TwigBox::getInstance();

- More than one template folder on runtime, even frontend-templates  
>  $oTwig->registerModule("aModuleName", "anyNamespace");
>  $oTwig->registerPath(__DIR__."/additional/directory", "secornd");

- Pre-definend globals (e.g. WB_PATH, WB_URL)
>  full list?

- Added operators "||" (or), "&&" (and) and "!" not to the template syntax
>  {% if (name && (surname != "admin") %}{% endif %}

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
