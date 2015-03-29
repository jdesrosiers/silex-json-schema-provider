<?php

namespace JDesrosiers\Silex\Provider;

use Jsv4;

class Jsv4Validator
{
    public function validate($data, $schema)
    {
        return Jsv4::validate($data, $schema);
    }

    public function isValid($data, $schema)
    {
        return Jsv4::isValid($data, $schema);
    }

    public function coerce($data, $schema)
    {
        return Jsv4::coerce($data, $schema);
    }
}
