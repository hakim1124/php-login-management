<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /** @test */
    public function TestRender()
    {
        View::render('Home/index', ["PHP Login Management"]);
        $this->expectOutputRegex('[html]');
        $this->expectOutputRegex('[body]');
        $this->expectOutputRegex('[Login Management]');
        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[Register]');
    }
}
