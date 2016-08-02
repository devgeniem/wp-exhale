# WP Plugin: Exhale
Developer friendly framework for creating xml data exports from wordpress.
It works only with php7.0 or better because we use scalar type hinting.

## Install plugin
```
$ composer require devgeniem/wp-exhale
$ wp plugin activate wp-exhale
```

## Usage
You need to define class which extends our parent class `Exhale\Base\XML`.
This class is autocalled when suitable request comes and you don't need to hook it anywhere.

For example this could look like:
```php
<?php
/**
 * This class exists so that users of Exhale can start producing xml really quickly
 */
abstract class MyProviderName implements \Exhale\Base\XML {
    /**
     * Returns exportable apartments to Vuokraovi
     */
    static public function get_export_data() : array {
        return array('item' => 'value');
    }

    /**
     * If you need to wrap your data into custom element you can use this
     * Empty array is ignored
     */
    static public function xml_root_element() : array {
        return array(
            'name' => 'wrapper'
        );
    }

    /**
     * This function can be used to map custom namespaces into the xml
     * Empty array is ignored
     */
    static public function xml_namespaces() : array {
        return array(
            'http://www.w3.org/2001/XMLSchema-instance' => 'xsi',
        );
    }
}
```

This class now automatically provides custom data export from:
`http://yoursite.com/api/export/myprovidername.xml`

With contents:
```xml
<wrapper xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <item>value</item>
</wrapper>
```

## How it works?
This plugin uses [sabre/xml](http://sabre.io/xml/writing/) inside.
The array from `get_export_data()` function is mapped into xml write operation in sabre/xml.
This way you get all the good things from sabre/xml as well.

## Settings
You can override default exporter url by defining in wp-config:
```php
define('EXHALE_URL_PREFIX','/my-custom/api/url/');
```
The earlier example would now be accessible from: `http://yoursite.com/api/export/myprovidername.xml`

## Special cases
If you want to add attributes to your elements or have multiple elements with same key you can do this:
```php
static public function get_export_data() {
        return array(
            [
                'name' => 'item',
                'attributes' => [
                    'url' => 'http://yoursite.com'
                ],
                'value' => 'value'
            ],
            [
                'name' => 'item',
                'attributes' => [
                    'url' => 'http://example.com'
                ],
                'value' => 'nothing'
            ],
        );
    }
```
This will produce following xml:
```xml
<item url="http://yoursite.com">value</item>
<item url="http://example.com">nothing</item>
```

## License
GPLv3
