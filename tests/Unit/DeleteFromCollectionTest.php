<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class DeleteFromCollectionTest extends AbstractTestCase
{

    public function testDeleteRecord() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $result = $collection->delete(1);
        $this->assertTrue($result);
    }

    public function testDeleteNotExistsKey() {
        try {
            $collection = HashCollectionFactory::createFilledHashCollection(100);
            $collection->delete('invalid hash');
            throw new Exception();
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(HashKeyDoesNotExists::class, $exception);
        }
    }

    public function testDeleteIgnoreNotExistsKey() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $result = $collection->delete('invalid hash', true);
        $this->assertFalse($result);
    }


    public function testDeleteFirstValue() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $collection->delete(1);
        $this->assertEquals(2, $collection->firstHash());
    }

    public function testDeleteLastValue() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $collection->delete(100);
        $this->assertEquals(99, $collection->lastHash());
    }

}
