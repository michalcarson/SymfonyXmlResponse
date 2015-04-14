# SymfonyXmlResponse
A simple XML Response formatter for Symfony.

## Is it pointless?
Someday, someone will probably tell me this whole class was unnecessary and if I just knew more about Symfony, I could have done this a much easier way.
That probably won't happen until I've poured a thousand hours into this project. But, still, tell me!

## The point
This class is an effor to make it possible to return an array from an action and have it rendered into XML. This should happen just the same way you can
return an array and have it rendered into JSON. The action (controller...whatever term you like) shouldn't have to know the format of the response,
just as it shouldn't know the format of the request.

I'm using this with [AOL/ATC](https://github.com/aol/atc). ATC is itself based on [Aura.Router](https://github.com/auraphp/Aura.Router). Within ATC, I have a
custom presenter that--once it determines the caller wants an XML response--simply does this:

```php
$xr = new XmlResponse();
$xr->root_element_name = $view;
$response = $xr->setData($data);
return $response;
```

In the code above, $data is an array and $view is a string.

