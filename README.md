# Honeybadger Tool For Laravel Nova

Display the occured Honeybadger faults along with your Laravel Nova resources.

![screenshot](https://beyondco.de/github/honeybadger/screenshot.png)

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require honeybadger-io/nova-honeybadger
```

Next, define your Honeybadger Project ID and API key inside your `config/services.php` file, like this:

```php
'honeybadger' => [
    'api_key' => env('HONEYBADGER_API_KEY'),
    'project_id' => env('HONEYBADGER_PROJECT')
]
```

## Usage

To display the Honeybadger faults that are associated with a given Nova resource, you need to add the Honeybadger Resource Tool to your resource.

To display all errors that are associated with a given Laravel User, all you need to do is add the resource tool to the `fields` method of your User resource.

For example, in your `app/Nova/User.php` file:

```php
public function fields(Request $request)
{
    return [
        ID::make()->sortable(),

        // Your other fields

        new \HoneybadgerIo\NovaHoneybadger\Honeybadger,

    ];
}
```

This will automatically search Honeybadger for faults with the resource's User ID.

### Defining Custom Context Using Resource Attributes

If you want to search for a different context key/value pair, you can create the resource tool using the `fromContextKeyAndAttribute` method.
This will tell Honeybadger to search for a specific context attribute and use the resource's column as the value.

For example, let's filter our Honeybadger faults by using the resource's email attribute:

```php
public function fields(Request $request)
{
    return [
        ID::make()->sortable(),

        // Your other fields

        \HoneybadgerIo\NovaHoneybadger\Honeybadger::fromContextKeyAndAttribute('context.user.email', 'email'),

    ];
}
```

### Defining Custom Context

To search your Honeybadger faults using a custom context key and a static value, you may use the `fromContextKeyAndValue` method.
It works similar to `withContextKeyAndAttribute`, but will use the second parameter as a static string, instead of looking it up as a resource attribute.

```php
public function fields(Request $request)
{
    return [
        ID::make()->sortable(),

        // Your other fields

        \HoneybadgerIo\NovaHoneybadger\Honeybadger::fromContextKeyAndAttribute('context.user.email', 'static.value@honeybadger.io'),

    ];
}
```

### Custom Search String

To get full control over your Honeybadger search string, you may use the `fromSearchString` method on the resource tool. This let's you define a completely custom search string, that will be used to lookup your Honeybadger faults.

```php
public function fields(Request $request)
{
    return [
        ID::make()->sortable(),

        // Your other fields

        \HoneybadgerIo\NovaHoneybadger\Honeybadger::fromSearchString('-tag:wip -tag:pending environment:"production"'),

    ];
}
```

If you only want to append a custom search string to your context attributes, you may use the `withSearchString` method in combination with the other methods:

```php
public function fields(Request $request)
{
    return [
        ID::make()->sortable(),

        // Your other fields

        (new \HoneybadgerIo\NovaHoneybadger\Honeybadger)->withSearchString('-environment:"production"'),

    ];
}
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [All Contributors](../../contributors)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
