<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\Config;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    /** @test */
    public function TestGetConnection()
    {
        $connection = Database::getConnection();
        self::assertNotNull($connection);
    }
    /** @test */
    public function TestGetConnectionSingleton()
    {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        self::assertSame($connection1, $connection2);
    }
}
