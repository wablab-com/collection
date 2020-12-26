<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setOutputCallback(function() {});
    }
}