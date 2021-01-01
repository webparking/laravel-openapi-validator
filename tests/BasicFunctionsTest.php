<?php

declare(strict_types=1);

namespace Webparking\OpenAPIValidator\Tests;

use League\OpenAPIValidation\PSR7\Exception\NoPath;

final class BasicFunctionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->yamlPath = __DIR__.'/_data/index.yaml';
    }

    public function testIfValidationSucceeds(): void
    {
        $this
            ->getJson('documented-endpoint')
            ->assertStatus(404);
    }

    public function testIfValidationFails(): void
    {
        $this->expectException(NoPath::class);

        $this
            ->getJson('undocumented-endpoint')
            ->assertStatus(404);
    }

    public function testIgnoreUri(): void
    {
        $this->ignoredUris = ['ignored-endpoint'];

        $this
            ->getJson('ignored-endpoint')
            ->assertStatus(404);
    }
}
