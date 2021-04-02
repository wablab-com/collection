<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class MoveLastTest extends AbstractTestCase
{



    public function testMovingFirstNodeToLast_TowNodesCollection()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(2);
        $this->assertEquals(2,$collection->count());
        $this->assertEquals(1, $collection->firstHash());
        $this->assertEquals(2, $collection->lastHash());
        $collection->moveLast($collection->firstHash());
        $this->assertEquals(2,$collection->count());
        $this->assertEquals(2, $collection->firstHash());
        $this->assertEquals(1, $collection->lastHash());
    }


    public function testMovingFirstNodeToLast_10NodesCollection()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertEquals(10,$collection->count());
        $this->assertEquals(1, $collection->firstHash());
        $this->assertEquals(10, $collection->lastHash());

        // move first to the last
        $collection->moveLast($collection->firstHash());
        $this->assertEquals(10,$collection->count());
        $this->assertEquals(2, $collection->firstHash());
        $this->assertEquals(1, $collection->lastHash());
    }

}
