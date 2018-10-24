The missing XML support for Laravel's Response class.

This package is designed to work with the [Laravel](https://laravel.com) framework.

## Installation

Install via composer:

```
composer require mtownsend/response-xml
```

### Registering the service provider

For Laravel 5.4 and lower, add the following line to your ``config/app.php``:

```php
/*
 * Package Service Providers...
 */
Mtownsend\ResponseXml\Providers\ResponseXmlServiceProvider::class,
```

For Laravel 5.5 and greater, the package will auto register the provider for you.

### Using Lumen

To register the service provider, add the following line to ``app/bootstrap/app.php``:

```php
$app->register(Mtownsend\ResponseXml\Providers\ResponseXmlServiceProvider::class);
```

## Quick start

### Respond with xml

```php
$data = [
    'status' => 'success',
    'data' => [
        'first_name' => 'John',
        'last_name' => 'Smith',
    ]
];
return response()->xml($data);

// Returns:
<?xml version="1.0"?>
<response>
    <status>success</status>
    <data>
        <first_name>John</first_name>
        <last_name>Smith</last_name>
    </data>
</response>
```

### Respond with xml from a collection or eloquent query

You may also pass a collection to be transformed into xml.

```php
return response()->xml(User::all());
```

### Respond with existing xml

If you already have xml, you can pass it to the ``xml`` method to respond.

```php
$xml = <<<XML
<?xml version="1.0"?>
<response>
    <status>success</status>
    <data>
        <first_name>John</first_name>
        <last_name>Smith</last_name>
    </data>
</response>
XML;
return response()->xml($xml);
```

### Respond with the request's preferred format (xml/json)

If you want to respond with either xml or json on the fly without writing if/else statements you may use the ``->preferredFormat()`` method. This will take the request's ``Accept`` header and try to determine which format the request wants to be served. If this header is nonexistent, it will default to json. **Note:** when using this method, it will automatically set your response's ``Content-Type`` header to match the request's ``Accept`` header.

```php
$data = [
    'status' => 'success',
    'data' => [
        'first_name' => 'John',
        'last_name' => 'Smith',
    ]
];
return response()->preferredFormat($data);
```

## Methods and arguments

**Response method**

``->xml($xml, $status = 200, array $headers = [], $xmlRoot = 'response')``

The ``$xml`` argument is the data you want to be transformed into xml (may also be a premade xml string).

``$status`` is the http code your response will send.

``$headers`` is an array of key/values of http headers your response will return. A default header of ``Content-Type: application/xml`` is automatically applied, but can be overwritten.

``$xmlRoot`` will change the root xml element. Default is ``response``.

**Response method**

``->preferredFormat($data, $status = 200, array $headers = [], $xmlRoot = 'response')``

See ``->xml()`` method arguments.

The only difference between this method and ``->xml()`` is ``$data`` can potentially be transformed to json and ``$xmlRoot`` will be ignored if the response is json.

## Purpose

Have you ever found yourself wishing Laravel offered the same exemplary support for returning XML responses as it does for JSON? Imagine you are creating an api platform and want to be inclusive of other apps that would prefer to make XML requests to your application - sometimes the reasons are more than preferential. Wouldn't it be a dream if you could return XML as simply as writing ``return response()->xml($data);`` and it just worked? **Now you can!**

This package achieves one critical goal: respond with XML as easily as you can with JSON in your Laravel application.

## Other packages you may be interested in

- [mtownsend/collection-xml](https://github.com/mtownsend5512/collection-xml)
- [mtownsend/request-xml](https://github.com/mtownsend5512/request-xml)
- [mtownsend/xml-to-array](https://github.com/mtownsend5512/xml-to-array)

## Credits

- Mark Townsend
- [Spatie](https://spatie.be/)
- [All Contributors](../../contributors)

## Testing

You can run the tests with:

```bash
./vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.