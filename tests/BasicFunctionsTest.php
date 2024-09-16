<?php

declare(strict_types=1);

namespace Webparking\OpenAPIValidator\Tests;

use League\OpenAPIValidation\PSR7\Exception\NoOperation;
use League\OpenAPIValidation\PSR7\Exception\NoPath;
use League\OpenAPIValidation\PSR7\Exception\NoResponseCode;

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

    public function testAssertNoOperation(): void
    {
        $this->expectException(NoOperation::class);

        $this
            ->postJson('documented-endpoint', [
                'attribute1' => 'value1',
                'attribute2' => 'value2',
            ])
            ->assertStatus(200);
    }

    public function testAssertNoOperationWithResponseCode(): void
    {
        $this->expectException(NoResponseCode::class);

        $this
            ->getJson('random-endpoint')
            ->assertStatus(200);
    }
}
