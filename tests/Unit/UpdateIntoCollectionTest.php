<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class UpdateIntoCollectionTest extends AbstractTestCase
{

    public function testUpdateRecord() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $result = $collection->update(1, 'test data');
        $this->assertTrue($result);
    }

    public function testUpdateNotExistsKey() {
        try {
            $collection = HashCollectionFactory::createEmptyHashCollection();
            $collection->update('invalid hash', 'test data');
            throw new Exception();
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(HashKeyDoesNotExists::class, $exception);
        }
    }

    public function testUpdateIgnoreNotExistsKey() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $result = $collection->update('invalid hash', 'test data', true);
        $this->assertFalse($result);
    }


    public function testUpdateOrInsert() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $collection->updateOrInsert(1, ['id' => 'updated']);
        $collection->updateOrInsert(500, ['id' => 'inserted']);
        $this->assertEquals('updated', $collection->find(1)['id']);
        $this->assertEquals('inserted', $collection->find(500)['id']);
    }

}
