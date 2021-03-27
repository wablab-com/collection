<?php

namespace WabLab\Tests\Unit;

use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class InsertBeforeTest extends AbstractTestCase
{

    public function testInsertBefore()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertEquals(10, $collection->count());
        $collection->insertBefore(6, 'new hash', 'new hash data');
        $this->assertEquals(11, $collection->count());
        $counter = 0;
        foreach($collection->yieldAll() as $hash => $value) {
            $counter++;
            if($hash == 'new hash') {
                $this->assertEquals($counter, 6);
            } elseif($hash > 5) {
                $this->assertEquals($counter, $hash+1);
            } else {
                $this->assertEquals($counter, $hash);
            }
        }
    }

    public function testInsertBeforeFirstNode_OneNodeCollectionList() {
        $collection = HashCollectionFactory::createFilledHashCollection(1);
        $this->assertEquals(1, $collection->count());
        $collection->insertBefore(1, 'new hash', 'new hash data');
        $this->assertEquals(2, $collection->count());
        $this->assertEquals(1, $collection->lastHash());
        $this->assertEquals('new hash', $collection->firstHash());
    }

    public function testInsertBeforeInvalidHash_ThrowException() {
        try {
            $collection = HashCollectionFactory::createFilledHashCollection(1);
            $collection->insertBefore('invalid hash', 'new hash', 'new hash data');
            throw new \Exception('no errors thrown');
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(HashKeyDoesNotExists::class, $exception);
        }
    }

    public function testInsertBeforeInvalidHash_DontThrowException() {
        $collection = HashCollectionFactory::createFilledHashCollection(1);
        $result = $collection->insertBefore('invalid hash', 'new hash', 'new hash data', true,true);
        $this->assertFalse($result);
    }
}
