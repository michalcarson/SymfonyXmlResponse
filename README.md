# SymfonyXmlResponse
A simple XML Response formatter for Symfony.

## Is it pointless?
Someday, someone will probably tell me this whole class was unnecessary and if I just knew more about Symfony, I could have done this a much easier way.
That probably won't happen until I've poured a thousand hours into this project. But, still, tell me!

## The point
This class is an effort to make it possible to return an array from an action and have it rendered into XML. This should happen just the same way you can
return an array and have it rendered into JSON. The action (controller...whatever term you like) shouldn't have to know the format of the response,
just as it shouldn't know the format of the request.

I'm using this with [AOL/ATC](https://github.com/aol/atc). ATC is itself based on [Aura.Router](https://github.com/auraphp/Aura.Router). Within ATC, I have a
custom presenter that--once it determines the caller wants an XML response--simply does this:

```php
$xr = new XmlResponse();

// changing the default root element
$xr->root_element_name = $view;

// data must be set after the root element
$response = $xr->setData($data);

return $response;
```

In the code above, <code>$data</code> is an array and <code>$view</code> is a string.

If the default root element name ("document") is acceptable, this can be further simplified to:

```php
$response = new XmlResponse($data);
return $response;
```
or
```php
return new XmlResponse($data);
```
or
```php
// using the static create() method
$response = XmlResponse::create($data);
return $response;
```

The static <code>create()</code> method and the constructor both accept additional optional parameters to specify the status code and an array of headers.
(The default status code is 200.)

```php
// specifying optional return code and headers
$response = new XmlResponse($data, 202, array('Foo-Header' => 'bar'));
return $response;
```

## Data array

The data passed to this class should be an associative array. Each array key represents an XML tag name and the array value represents the content of the XML tag.

```php
$data = array(
    'foo' => 'bar',
    'argle' => 'bargle'
);
$response = new XmlResponse($data);
return $response;
```

The array above will produce this XML output:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<document>
  <foo>bar</foo>
  <argle>bargle</argle>
</document>
```

## Attributes

You can specify attributes to a tag by passing a specially constructed array for the value of that tag. Within this array, each attribute should be represented
as a key value pair with the key prefixed by '@'. The actual value of the tag should have a key which repeats the original key name. All attribute key-value pairs
must come before the actual value entry.

Keep in mind, this is crossing some boundaries for an action to supply XML attributes. If this array were rendered as JSON instead of XML, it would probably not
be usable. This design is flawed and will need to be changed in the future.

```php
$data = array(
    'foo' => array(
        '@buzz' => 'boom',
        '@bing' => 'bam',
        'foo' => 'bar'       // same key name as parent
    ),
    'argle' => 'bargle'
);
$response = new XmlResponse($data);
return $response;
```

The array above will produce this XML output:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<document>
  <foo buzz="boom" bing="bam">bar</foo>
  <argle>bargle</argle>
</document>
```

## Repeating Elements

XML can have repeating elements.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<document>
  <lorem>ipsum</lorem>
  <files>
    <file><name>file1.txt</name><size>10K</size></file>
    <file><name>file2.txt</name><size>20K</size></file>
    <file><name>file3.txt</name><size>8K</size></file>
  </files>
  <et>dolore</et>
</document>
```

PHP arrays (and JavaScript objects) do not support this without some very contrived structures. In order to support repeating
fields, use the XmlRepeater class. XmlRepeater acts as a decorator to the XmlResponse class.

You may add as many XmlRepeater instances as you need. Each of them takes a placeholder, an element name and an array of data.
The class will turn the array into XML, wrap it in an element with the given name and insert it into the XmlResponse result
in place of the placeholder.

```php
// associative array for XmlResponse
$data = [
    'lorem' => 'ipsum',
    'files' => '@filesPlaceHolder@'
    'et' => 'dolore'
];

// indexed array for the repeating data
$file_array = [
    ['name' => 'file1.txt', 'size' => '10K'],
    ['name' => 'file2.txt', 'size' => '20K'],
    ['name' => 'file3.txt', 'size' => '8K']
];

$repeater = new XmlRepeater('@filesPlaceHolder@', 'file', $file_array);

$response = new XmlResponse($data);
$response->addDecorator($repeater);
return $response;
```

This code would produce the XML document above with repeating "file" elements inside the "files" element.

## License

Licensed under the MIT license.
