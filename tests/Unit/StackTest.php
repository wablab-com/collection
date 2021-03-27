<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class StackTest extends AbstractTestCase
{


    public function testPullOffStack() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $hash = 0;
        $collection->pullOffStack($hash);
        $this->assertEquals(100, $hash);
    }

    public function testEmptyCollectionPullOffStack() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $this->assertNull($collection->pullOffStack());
        $this->assertNull($collection->last());
        $this->assertNull($collection->lastHash());
    }

    public function testPullOffStackEverythingFromCollection() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $counter = 0;
        do {
            $counter++;
            $collection->pullOffStack();
        } while($collection->first());
        $this->assertEquals(100, $counter);
        $this->assertEquals(0, $collection->count());
        $this->assertNull($collection->first());
        $this->assertNull($collection->last());
    }

    public function testPullOffStackHalfFromCollection() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $counter = 0;
        do {
            $counter++;
            $collection->pullOffStack();
        } while($counter < 50);
        $this->assertEquals(50, $collection->count());
        $this->assertEquals(1, $collection->firstHash());
        $this->assertEquals(50, $collection->lastHash());
    }

}
