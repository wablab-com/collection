<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class QueueTest extends AbstractTestCase
{

    public function testPullOffQueue() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $hash = 0;
        $collection->pullOffQueue($hash);
        $this->assertEquals(1, $hash);
    }

    public function testEmptyCollectionPullOffQueue() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $this->assertNull($collection->pullOffQueue());
        $this->assertNull($collection->first());
        $this->assertNull($collection->firstHash());
    }

    public function testPullOffQueueEverythingFromCollection() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $counter = 0;
        do {
            $counter++;
            $collection->pullOffQueue();
        } while($collection->first());
        $this->assertEquals(100, $counter);
        $this->assertEquals(0, $collection->count());
        $this->assertNull($collection->first());
        $this->assertNull($collection->last());
    }


    public function testPullOffQueueHalfFromCollection() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $counter = 0;
        do {
            $counter++;
            $collection->pullOffQueue();
        } while($counter < 50);
        $this->assertEquals(50, $collection->count());
        $this->assertEquals(51, $collection->firstHash());
        $this->assertEquals(100, $collection->lastHash());
    }

}
