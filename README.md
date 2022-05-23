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

You have to write a boilerplate and messy code every time that a specific scenario would happen.
Either you have to separate update and create related form requests into separate files, or you have to do something
like the above code.

Form request separation has its own disadvantages like you repeating the validations, and it's not practical.

**So what's the solution?**

The `RulesBuilder` comes for the rescue. It has an advance caching functionality
so that you can put all the update and create validations in a
single form request file and divide the rules, based on the request method
with a clean syntax.

## Usage

### Code

It's nothing fancy. Whenever you need to validate a request and the
rules may change based on request method you can use the `RulesBuilder`.

Let's say you want to make a CRUD for customers. You create a CustomerRequest
and you want to validate the data for update and create. You have some common validations
between two methods but each method has specific extra rules. Here is how you can do it:

```php

use SaliBhdr\ValidationRules\Facades\RulesBuilder;

class CustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return RulesBuilder::build()
               ->any([
                   "name"   => "required|string|max:100",
                   "family" => "required|string|max:100",
                   'role'   => 'required|string|max:20',
                   "email"  => "required|email",
                   "mobile" => "required|digits:11",
               ])
               ->create([
                   "email"    => "unique:admins",
                   "mobile"   => "unique:admins",
                   'role'     => 'required',
               ])
               ->update([
                   'is_active' => 'required|boolean',
                   "email"     => [Rule::unique('admins')->ignore($this->customer)],
                   "mobile"    => [Rule::unique('admins')->ignore($this->customer)],
               ])
               ->rules();
    }
}

```

### Commands

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
Please read the [CONTRIBUTION.md][link-contribution] file before any contributions.

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
