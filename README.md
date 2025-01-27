# laravel-openapi-validator
An OpenAPI documentation validator using your existing Laravel tests.

## TL;DR
`composer require --dev webparking/laravel-openapi-validator`

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Webparking\OpenAPIValidator\ValidatesHttpMessagesAgainstDocs;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use ValidatesHttpMessagesAgainstDocs;
}
```

## Laravel Version Compatibility
The Laravel versions listed below are all currently supported:

- Laravel >= 11.x.x on PHP >= 8.3 is supported starting from 3.0.0
- Laravel >= 10.x.x on PHP >= 8.2 is supported starting from 2.2.0
- Laravel >= 9.x.x on PHP >= 8.1 is supported starting from 2.1.0
- Laravel >= 8.2.x on PHP >= 8.0 is supported starting from 2.0.0

## The problem
When creating and maintaining an HTTP REST API, you want to make sure it works well and is well documented. We consider it essential to be able to check both automatically.

## What we tried before
1.  We made a system that would generate tests from the API documentation.
    For the majority of tests, it worked. But having a simple way to include edge cases in your tests is next to impossible. For example: there's an endpoint that returns a 500 status code when called with certain input. You've fixed the bug and want to add a test to make sure it actually is and stays fixed. To do that, you'd have to include an error-case in your docs. You don't want to live in a hacky world like that.
1.  We made a system that would generate documentation based on the application's code.
    First of all, it would be very difficult to explain nuances and abstract concepts in your documentation, as they don't have a formal place in the application. We'd have to split the documentation in two: the technical spec generated from code and the complimentary explanations in a wiki of some kind. And yes, they'd get out of sync over time.
    Second, your application code needs to be very very structured in order to accomplish this. Maintaining quality by consistency is a big thing for us, but in practice there are always one or two edge cases where it's actually better to deviate.

## Our solution
We combined [Laravel HTTP testing](https://laravel.com/docs/master/http-tests) with [ThePHPLeague's OpenAPI PSR7 validator](https://github.com/thephpleague/openapi-psr7-validator) by overriding [MakesHttpRequests#call](https://github.com/laravel/framework/blob/master/src/Illuminate/Foundation/Testing/Concerns/MakesHttpRequests.php). Before executing the request, we have it validated against the OpenAPI documentation. And before returning the response, we have that validated too. Exceptions are thrown for any problems found.

This solution has enabled us to use both OpenAPI and HTTP tests to the fullest; no compromises needed.

## Usage
Simply use the `ValidatesHttpMessagesAgainstDocs` in your test class. We generally do this on the project level [TestCase](https://github.com/laravel/laravel/blob/master/tests/TestCase.php).

The default index file for the docs is `docs/index.yaml`, but you're free to override that by setting the `yamlPath` property.

You may want to exclude certain URI's from validation. Set the `ignoredUris` property to do this.
