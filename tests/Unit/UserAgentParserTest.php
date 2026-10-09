<?php

namespace Tests\Unit;

use App\Services\UserAgentParser;
use PHPUnit\Framework\TestCase;

class UserAgentParserTest extends TestCase
{
    public function test_families(): void
    {
        $this->assertNull(UserAgentParser::family(null));
        $this->assertSame('Chrome', UserAgentParser::family('Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 Chrome/120.0 Safari/537.36'));
        $this->assertSame('Edge', UserAgentParser::family('Mozilla/5.0 AppleWebKit/537.36 Chrome/120.0 Safari/537.36 Edg/120.0'));
        $this->assertSame('Firefox', UserAgentParser::family('Mozilla/5.0 (X11; Linux) Gecko/20100101 Firefox/121.0'));
        $this->assertSame('Safari', UserAgentParser::family('Mozilla/5.0 (iPhone) AppleWebKit/605.1.15 Version/17.0 Mobile/15E148 Safari/604.1'));
        $this->assertSame('Bot', UserAgentParser::family('Googlebot/2.1 (+http://www.google.com/bot.html)'));
        $this->assertSame('Bot', UserAgentParser::family('WhatsApp/2.23'));
        $this->assertSame('Other', UserAgentParser::family('SomethingElse/1.0'));
    }
}
