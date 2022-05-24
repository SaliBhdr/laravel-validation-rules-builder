# Laravel Validation Rules Builder

![SaliBhdr|typhoon][link-logo]

[![Total Downloads][ico-downloads]][link-downloads]
[![Required Laravel Version][ico-laravel]][link-packagist]
[![Required PHP Version][ico-php]][link-packagist]
[![Latest Versions][ico-version]][link-packagist]
[![License][ico-license]][link-packagist]
[![Today Downloads][ico-today-downloads]][link-downloads]

## Introduction

ReadMe will be added soon

**Features**

## Installation

Install with composer:

```sh
 $ composer require salibhdr/laravel-validation-rules-builder
```

For Laravel < 5.5 Register the Service provider in your config/app.php configuration file:

```
'providers' => [

     # Other service providers...

     SaliBhdr\ValidationRule\ServiceProvider::class,
],
```

Publish the config file:

```sh
  php artisan vendor:publish --provider="SaliBhdr\ValidationRules\ServiceProvider"
```

## Getting started

There are times in Laravel form requests that you need to check the method of
the request and decide to add some extra rule. You would do something like this:

```php

class FooRequest extends FormRequest
{
    public function rules()
    {
        $rules = [...]

        if($this->method() == 'POST')
          $rules = array_merge($rules, [...])

        return $rules;
    }
}

```

You have to write a boilerplate and messy code every time this kind of scenario would happen.
Either you have to split the form request for each method into a separate file,
or you have to do something like the above code.

Form request separation has its own disadvantages like you repeating the validation rules,
and it's not practical.

**So what's the solution?**

The `RulesBuilder` comes for the rescue. It has an advance caching functionality
so that you can put all the update and create validations in a
single form request file and divide the rules, based on the request method
with a clean syntax.

## Dictionary

**HTTP request methods:**

HTTP defines a set of request methods to indicate the desired action to be performed for a
given resource. Although they can also be nouns, these request methods are sometimes referred
to as HTTP verbs. Each of them implements a different semantic.

**Available HTTP Methods:**
  - GET
  - POST
  - PUT
  - HEAD
  - DELETE
  - PATCH
  - OPTIONS
  - CONNECT
  - TRACE

**As you know the most common ones are:**
- GET
- POST
- PUT
- PATCH
- DELETE

## Usage

### Basic Usage

It's nothing fancy. Whenever you need to validate a request and the
rules may differ based on the request's HTTP method you can
use the `RulesBuilder`.

Let's say you want to implement a CRUD functionality for customers,
and you decide to validate request both on create and update methods.
The create endpoint accepts `POST` HTTP method and the update endpoint
accepts `PATCH` HTTP method. Obviously you have some common validations
between two methods but each method has specific extra rules. Here is
how you can do that with `RulesBuilder`:

```php

use SaliBhdr\ValidationRules\Facades\RulesBuilder;

class CustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return RulesBuilder::build()
               // Common validation rules between POST and PATCH
               ->any([
                   "name"     => "required|string|max:100",
                   "family"   => "required|string|max:100",
                   'role'     => 'required|string|max:20',
                   "email"    => "required|email",
                   "mobile"   => "required|digits:11",
                   "password" => 'string|min:6',
               ])
               // The validation rules that
               // should be applied when the HTTP method is POST
               ->post([
                   "email"    => "unique:admins",
                   "mobile"   => "unique:admins",
                   'password' => 'required',
               ])
               // The validation rules that
               // should be applied when the HTTP method is PATCH
               ->patch([
                   'is_active' => 'required|boolean',
                   "email"     => [Rule::unique('admins')->ignore($this->customer)],
                   "mobile"    => [Rule::unique('admins')->ignore($this->customer)],
               ])
               // Returns the array of resolved rules
               ->rules();
    }
}

```

**Explanation:**

The validation rules specified in the `any()` method will be applied in all HTTP methods.
After that, based on the request's HTTP method, the validation rules of that
method - post or patch - will be merged with the common rules.
finally, we can fetch the result array with the `rules()` method.

### Config

The `RulesBuilder` caches the resolved rules into a file. You can disable the cache for the
local development environment or change the path of the cache file. Here is the config:

```php

return [

    /*
    |--------------------------------------------------------------------------
    | Rules Cache
    |--------------------------------------------------------------------------
    |
    | This value is defines the cache strategy. This value is used when the
    | package needs to define the cache path or status.
    |
    | path : The location of the rules cache file. remember to provide the valid permissions.
    |
    | enable : This option enables or disables cache in the whole application.
    |          You can also customize the cache config for each rule builder separately.
    */
    'cache' => [

        'path' => storage_path('framework/cache/rules'),

        'enable' => env('VALIDATION_RULE_CACHE_ENABLE', true),
    ],
];

```

You can put the `VALIDATION_RULE_CACHE_ENABLE` to the .env file to
disable or enable caching for each environment:

```dotenv
VALIDATION_RULE_CACHE_ENABLE=true
```

### Commands

Here are the list of all commands that are available:

```sh
    # You can clea cache with rule:clear command
    rule:clear         Remove the rule cache file

    # You can see the list of rules that are cached
    rule:list          List of all cached rules
```

# import commands
iran:import                  Imports all regions into the database (Can be selected by option)


**Examples:**

Issues
----
You can report issues in Github repository [here][link-issues]

License
----
Laravel validation rules builder is released under the MIT License.

Created by [Salar Bahador][link-github]

Linked In Address : [Linkedin][link-linkedin]

Built with ❤ for you.

Contributing
----
Please read the [CONTRIBUTION.md][link-contribution] file.

Contributions, useful comments, and feedback are most welcome!

[ico-laravel]: https://img.shields.io/badge/Laravel-≥5.0-ff2d20?style=flat-square&logo=laravel

[ico-php]: https://img.shields.io/badge/php-≥7.0-8892bf?style=flat-square&logo=php

[ico-downloads]: https://poser.pugx.org/salibhdr/laravel-validation-rules-builder/downloads

[ico-today-downloads]: https://img.shields.io/packagist/dd/salibhdr/laravel-validation-rules-builder.svg?style=flat-square

[ico-license]: https://poser.pugx.org/salibhdr/laravel-validation-rules-builder/v/unstable

[ico-version]: https://img.shields.io/packagist/v/salibhdr/laravel-validation-rules-builder.svg?style=flat-square

[link-logo]: https://drive.google.com/a/domain.com/thumbnail?id=12yntFCiYIGJzI9FMUaF9cRtXKb0rXh9X

[link-packagist]: https://packagist.org/packages/salibhdr/laravel-validation-rules-builder

[link-downloads]: https://packagist.org/packages/salibhdr/laravel-validation-rules-builder/stats

[link-packagist]: https://packagist.org/packages/salibhdr/laravel-validation-rules-builder

[link-contribution]: https://github.com/Salibhdr/laravel-validation-rules-builder/blob/master/CONTRIBUTING.md

[link-issues]: https://github.com/Salibhdr/laravel-validation-rules-builder/issues

[link-github]: https://github.com/Salibhdr

[link-linkedin]: https://www.linkedin.com/in/salar-bahador
