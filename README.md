# Rinvex Widgets

**Rinvex Widgets** is a powerful and easy to use widget system, that combines the both the power of code logic and the flexibility of template views. You can create asynchronous widgets, reloadable widgets, and use the console generator to auto generate your widgets, all out of the box.

[![Packagist](https://img.shields.io/packagist/v/rinvex/widgets.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/rinvex/widgets)
[![VersionEye Dependencies](https://img.shields.io/versioneye/d/php/rinvex:widgets.svg?label=Dependencies&style=flat-square)](https://www.versioneye.com/php/rinvex:widgets/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/rinvex/widgets.svg?label=Scrutinizer&style=flat-square)](https://scrutinizer-ci.com/g/rinvex/widgets/)
[![Code Climate](https://img.shields.io/codeclimate/github/rinvex/widgets.svg?label=CodeClimate&style=flat-square)](https://codeclimate.com/github/rinvex/widgets)
[![Travis](https://img.shields.io/travis/rinvex/widgets.svg?label=TravisCI&style=flat-square)](https://travis-ci.org/rinvex/widgets)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/7923f41b-09fc-40f1-ae8e-7d19afae897c.svg?label=SensioLabs&style=flat-square)](https://insight.sensiolabs.com/projects/7923f41b-09fc-40f1-ae8e-7d19afae897c)
[![StyleCI](https://styleci.io/repos/98805007/shield)](https://styleci.io/repos/98805007)
[![License](https://img.shields.io/packagist/l/rinvex/widgets.svg?label=License&style=flat-square)](https://github.com/rinvex/widgets/blob/develop/LICENSE)


## Installation

1. Install the package via composer:
    ```shell
    composer require rinvex/widgets
    ```
2. Done!


## Usage

### Create Your Widget

Let's assume that we want to make a list of recent posts as a widget, and reuse it in several locations.

First, we can create a Widget class using the following command:

```ssh
php artisan make:widget RecentPosts
```

That command will generate a new widget class with `App\Widgets\RecentPosts` namespace, and located at `app/Widgets/RecentPosts.php` path by default.

The basic content of a widget is as follows:

```php
<?php

namespace App\Widgets;

use Rinvex\Widgets\Models\AbstractWidget;

class RecentPosts extends AbstractWidget
{
    /**
     * Treat this method as a normal PHP method, or a controller action.
     * You may return view() or other content to render and display.
     */
    public function make()
    {
        //
    }
}
```

All widgets **MUST** extend the `Rinvex\Widgets\Models\AbstractWidget`, and **MUST** have a public `make` method, you can return whatever you want from inside that method, with complete freedom and full flexbility to write your own logic and return your results whether it's pure data array/json, or rendered view or whatever. It's really up to you. Treat this method as a normal PHP method, or a controller action. You may return `view('view.path.here')` or other content to be rendered and displayed.

The `Widget::make()` method is resolved via [Service Container](https://laravel.com/docs/master/container), so method injection is also available here.

### Call Your Widgets

For consistency purposes there's a calling convensions for widgets, which adheres to Laravel's own methods on calling *callables* as such calling controller actions for example `App\Http\Controllers\ExampleController@action`, so as follows the way you may call your newly created widget:

```php
$recentPosts = Widget::make('App\Widgets\RecentPosts');
```

Now you can use that `$recentPosts` anywhere you want, it contains the widget result.

For your convenience, **Rinvex Widgets** include also a widget helper for easy usage. Example:

```blade
$recentPosts = widget('App\Widgets\RecentPosts');
```

You can also call widgets from within views using blade directive with same signature as follows:

```blade
@widget('App\Widgets\RecentPosts')
```

The `Widget::make()` method takes three parameters, the first one the only mandatory parameter which is the namespace of the widget you're calling, the second one is any parameters you would like to pass to widget's `make` method, the third and the last one is a boolean flag with true or false for asynchronous loading, will talk about that later.

### Passing variables to widget

```php
$recentPosts = Widget::make('App\Widgets\RecentPosts', ['param1' => 'Value #1'], true);
```

As you can see, this widget is now loaded asynchronous.

### Asynchronous widgets

In some situations it can be very beneficial to load widget content with AJAX.

Fortunately, this can be achieved very easily! All you need to do is to pass `true` as the third parameter to the Widget Facade or the blade directive. Example: `Widget::make('Widget\Class\Path', [], true)`, and simirally `@widget('Widget\Class\Path', [], true)`.

> **Note:** Widget params are encrypted and sent via async call. Expect them to be json_encoded and json_decoded before and afterwards.

By default nothing is shown until async call is finished. This can be customized by adding a `placeholder()` method to the widget class, and return any string to be displayed. See the following example:

```php
public function placeholder()
{
    return 'Loading...';
}
```

> **Notes:**
> - **Rinvex Widgets** package auto register a new route with `rinvex.widgets.async` name, which could be accessed via `http://yourproject.app/widget`. That route accepts async widget calls, with required parameters, all encrypted and process it then return the response.
> - If you need to modify the default route definition or behaviour, you may need to copy `Rinvex\Widgets\Providers\WidgetsServiceProvider` to your app, modify it according to your needs and register it in your Laravel application instead of the default one. In such case you may need to disable Laravel Auto Discovery for this package **Rinvex Widgets**.

### Reloadable widgets

You can go even further and automatically reload widget every N seconds.

Just set the `$reloadTimeout` property of the widget class and you are done.

```php
class RecentPosts extends AbstractWidget
{
    /**
     * The number of seconds before each reload.
     *
     * @var float
     */
    protected $reloadTimeout;
}
```

Both sync and async widgets can become reloadable. You should use this feature with care, because it can easily spam your app with async calls if timeouts are too low.

In case you need short response rate or realtime, you may have to consider using web sockets which is better in such case, but it's not implemented by default in this package.

### Container

Async and Reloadable widgets both require some DOM interaction so they wrap all widget output in a html container. This container is defined by `AbstractWidget::container()` method and can be customized too.

To make it easy, flexible, cacheable, better in performance, and even overridable, we made that container accepts a view path, so you can use whatever syntax you want in that view including of course the lovely blade tags and directives.

Just set the `$container` property of the widget class and you are done.

```php
class RecentPosts extends AbstractWidget
{
    /**
     * The widget container template.
     *
     * @var string
     */
    protected $container = 'rinvex/widgets::container';
}
```

> **Note:** Nested async or reloadable widgets are not supported.

### Widget Groups

In most cases Blade is a perfect tool for setting the position and order of widgets. However, sometimes you may find useful to approach widgets from a columns perspective as follows:

```php
// add several widgets to the 'sidebar' group anywhere you want
Widget::group('sidebar')->addWidget('widgetName1', $params);
Widget::group('sidebar')->addWidget('widgetName1', $params, true);
Widget::group('sidebar')->addWidget('widgetName1', $params, true, 50);
```

The `Widget::addWidget()` method accepts four parameters, the first three are the same as `Widget::make()` exactly, and the fourth optional parameters is the position where you'd like to place that widget in that group. Position?! Yes, exactly. You can order widgets inside each group, and you can imagine widget groups as a CMS columns, that way you can structure your page the way you want.

To render a widget group and print the output you can do the following:

```php
$sidebar = Widget::group('sidebar')->render();
```

Or using blade syntax:

```blade
@widgetGroup('sidebar')
```

And if you want to get all your widget groups collection, you can do so as follows:

```php
$groups = Widget::groups();
```

> **Notes:**
> - Both results of `Widget::group('sidebar')` and `Widget::groups()` are collections, and you can utilize it exactly as you do with any [Laravel Collections](https://laravel.com/docs/master/collections).
> - The `Widget::group('sidebar')` returns a collection of widgets, and `Widget::groups()` returns a collection of widget groups.

You can set a separator that will be display between widgets inside the same group as follows:

```php
Widget::group('sidebar')->separateWith('<hr>')->...;
```

You can also wrap each widget in a group using `wrap` method like that.

```php
Widget::group('sidebar')->wrap(function ($key, $widget, $content) {
    return "<div class='widget-{$key}'>{$content}</div>";
})->...;
```

The `wrap()` method accept a callback, that accepts three variables. The first one is the index/key of that widget in the group, the second one is the widget object itself, and the third one is the rendered content of the widget as HTML string. Note that you've full access to the whole widget group collection, so you can query the total number of widgets for example `$this->count()` or any other information you may need to utilize.

Checking the state of a widget group:

```php
// Check if widget group is empty or not
Widget::group('sidebar')->isEmpty(); // bool

// Get widgets count in the widget group
Widget::group('sidebar')->count(); // int
```

And similarly you can check the state of all groups too:

```php
// Check if there's any widget groups or not
Widget::groups()->isEmpty(); // bool

// Get widget groups count
Widget::groups()->count(); // int
```

> **Notes:**
> - The `Widget` class is a facade, that's auto registered into your Laravel application, so you may call it anywhere even inside your controllers, or views. You may call the aliased facade as `Widget` only, or call the fully qualified namespace as `Rinvex\Widgets\Facades\Widget`, no difference.
> - The `Widget` facade has a fluent API, which means you can intuitively chain methods with ease.


## Changelog

Refer to the [Changelog](CHANGELOG.md) for a full history of the project.


## Support

The following support channels are available at your fingertips:

- [Chat on Slack](http://chat.rinvex.com)
- [Help on Email](mailto:help@rinvex.com)
- [Follow on Twitter](https://twitter.com/rinvex)


## Contributing & Protocols

Thank you for considering contributing to this project! The contribution guide can be found in [CONTRIBUTING.md](CONTRIBUTING.md).

Bug reports, feature requests, and pull requests are very welcome.

- [Versioning](CONTRIBUTING.md#versioning)
- [Pull Requests](CONTRIBUTING.md#pull-requests)
- [Coding Standards](CONTRIBUTING.md#coding-standards)
- [Feature Requests](CONTRIBUTING.md#feature-requests)
- [Git Flow](CONTRIBUTING.md#git-flow)


## Security Vulnerabilities

If you discover a security vulnerability within this project, please send an e-mail to [help@rinvex.com](help@rinvex.com). All security vulnerabilities will be promptly contacted.


## About Rinvex

Rinvex is a software solutions startup, specialized in integrated enterprise solutions for SMEs established in Alexandria, Egypt since June 2016. We believe that our drive The Value, The Reach, and The Impact is what differentiates us and unleash the endless possibilities of our philosophy through the power of software. We like to call it Innovation At The Speed Of Life. That’s how we do our share of advancing humanity.


## License

This software is released under [The MIT License (MIT)](LICENSE).

(c) 2016-2017 Rinvex LLC, Some rights reserved.
