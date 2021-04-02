<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class MoveFirstTest extends AbstractTestCase
{



    public function testMovingLastNodeToFirst_TowNodesCollection()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(2);
        $this->assertEquals(2,$collection->count());
        $this->assertEquals(1, $collection->firstHash());
        $this->assertEquals(2, $collection->lastHash());
        $collection->moveFirst($collection->lastHash());
        $this->assertEquals(2,$collection->count());
        $this->assertEquals(2, $collection->firstHash());
        $this->assertEquals(1, $collection->lastHash());
    }


    public function testMovingLastNodeToFirst_10NodesCollection()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertEquals(10,$collection->count());
        $this->assertEquals(1, $collection->firstHash());
        $this->assertEquals(10, $collection->lastHash());

        // move first to the last
        $collection->moveFirst($collection->lastHash());
        $this->assertEquals(10,$collection->count());
        $this->assertEquals(10, $collection->firstHash());
        $this->assertEquals(9, $collection->lastHash());
    }

}
