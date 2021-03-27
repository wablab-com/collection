<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class BasicCollectionTest extends AbstractTestCase
{

    public function testFindExistsKey() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $this->assertNotNull($collection->find(1));
    }

    public function testFindNotExistsKey() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $this->assertNull($collection->find('invalid hash'));
    }

    public function testGetCountInEmptyCollection() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $this->assertEquals(0, $collection->count());
    }

    public function testGetCountInFilledCollection() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $this->assertEquals(100, $collection->count());
    }

    public function testGetFirstInEmptyCollection() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $this->assertNull($collection->first());
    }

    public function testGetFirstInFilledCollection() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $hash = 0;
        $this->assertIsArray($collection->first($hash));
        $this->assertEquals(1, $hash);
    }

    public function testGetLastInEmptyCollection() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $this->assertNull($collection->last());
    }

    public function testGetLastInFilledCollection() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $hash = 0;
        $this->assertIsArray($collection->last($hash));
        $this->assertEquals(100, $hash);
    }

    public function testIsset() {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertTrue($collection->isset(5));
        $this->assertFalse($collection->isset('invalid key'));
    }

}
