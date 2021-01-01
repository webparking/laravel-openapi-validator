<?php

declare(strict_types=1);

namespace Webparking\OpenAPIValidator\Tests;

use Webparking\OpenAPIValidator\ValidatesHttpMessagesAgainstDocs;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use ValidatesHttpMessagesAgainstDocs;
}
