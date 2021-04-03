<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class OffsetTest extends AbstractTestCase
{

    public function testOffset_FilledCollection()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $offset5 = $collection->offset(5);
        $this->assertEquals(6, $offset5['id']);

        $offset0 = $collection->offset(0);
        $this->assertEquals(1, $offset0['id']);

        $offset9 = $collection->offset(9);
        $this->assertEquals(10, $offset9['id']);

    }

    public function testInvalidOffset()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertNull($collection->offset(15));
    }

}
