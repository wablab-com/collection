<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;

class InsertToCollectionTest extends AbstractTestCase
{

    public function testInsertNewRecords() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $result = $collection->insert('test_hash', 'test data');
        $this->assertTrue($result);
    }

    public function testInsertDuplicateRecord() {
        try {
            $collection = HashCollectionFactory::createEmptyHashCollection();
            $collection->insert('test_hash', 'test data');
            $collection->insert('test_hash', 'test data');
            throw new Exception();
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(HashKeyAlreadyExists::class, $exception);
        }
    }

    public function testInsertIgnoreDuplicateRecord()
    {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $collection->insert('test_hash', 'test data');
        $result = $collection->insert('test_hash', 'test data', true);
        $this->assertFalse($result);
    }

}
