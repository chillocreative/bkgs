<?php

namespace Tests\Feature;

use App\Support\PhoneFormatter;
use InvalidArgumentException;
use Tests\TestCase;

class PhoneFormatterTest extends TestCase
{
    public function test_normalises_local_format(): void
    {
        $this->assertSame('60123456789', PhoneFormatter::toSendora('0123456789'));
    }

    public function test_normalises_with_plus(): void
    {
        $this->assertSame('60123456789', PhoneFormatter::toSendora('+60123456789'));
    }

    public function test_normalises_already_canonical(): void
    {
        $this->assertSame('60123456789', PhoneFormatter::toSendora('60123456789'));
    }

    public function test_normalises_with_spaces_and_dashes(): void
    {
        $this->assertSame('60123456789', PhoneFormatter::toSendora('012-345 6789'));
    }

    public function test_rejects_garbage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PhoneFormatter::toSendora('abc');
    }

    public function test_rejects_too_short(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PhoneFormatter::toSendora('012');
    }
}
