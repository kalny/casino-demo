<?php

namespace Tests\Unit\Domain\Common\ValueObjects;

use App\Domain\Common\ValueObjects\Email;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use Tests\TestCase;

class EmailTest extends TestCase
{
    public function testCreatesValidEmailFromString(): void
    {
        $email = new Email('test@example.com');

        $this->assertSame('test@example.com', $email->getValue());
    }

    public function testNormalizeEmailToLowerCase(): void
    {
        $email = new Email('Test@Example.com');

        $this->assertSame('test@example.com', $email->getValue());
    }

    public function testTrimEmailWhiteSpaces(): void
    {
        $email = new Email('  test@example.com  ');

        $this->assertSame('test@example.com', $email->getValue());
    }

    public function testCreateEmailWithIncorrectFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Email('incorrect');
    }

    public function testCreateEmailFromEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Email(' ');
    }
}
