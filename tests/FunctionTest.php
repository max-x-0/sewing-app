<?php

use PHPUnit\Framework\TestCase;

require_once 'functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'use_cookies' => false,
        'use_only_cookies' => false
    ]);
}


class FunctionTest extends TestCase
{
    public function testHtmlEscaping()
    {
        $this->assertEquals(
            'текст &lt;script&gt;alert(1)&lt;/script&gt;',
            e('текст <script>alert(1)</script>')
        );
        $this->assertEquals('&#039;тест&#039;', e("'тест'"));
    }

    public function testCsrfTokenGenerationAndVerification()
    {
        unset($_SESSION['csrf_token']);
        $token = generate_csrf_token();
        $this->assertNotEmpty($token);
        $this->assertTrue(isset($_SESSION['csrf_token']));

        $this->assertTrue(verify_csrf_token($token));

        $this->assertFalse(verify_csrf_token('wrong_token'));
    }
}
