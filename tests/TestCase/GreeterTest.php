<?php

declare(strict_types=1);

namespace Tests\TestCase;

use Cadasto\TemplateRepo\Greeter;
use PHPUnit\Framework\TestCase;

final class GreeterTest extends TestCase
{
    public function testGreetsByName(): void
    {
        $greeter = new Greeter();

        self::assertSame('Hello, Developer!', $greeter->greet('Developer'));
    }
}
