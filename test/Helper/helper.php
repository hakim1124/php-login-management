<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\App {
    function header(string $value)
    {
        echo $value;
    }
}

namespace ProgramerZamanNow\Belajar\PHP\MVC\Service {
    function setcookie(string $name, string $value)
    {
        echo "$name: $value";
    }
}
