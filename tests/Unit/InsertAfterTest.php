<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class InsertAfterTest extends AbstractTestCase
{

    public function testInsertAfter()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertEquals(10, $collection->count());
        $collection->insertAfter(5, 'new hash', 'new hash data');
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

    public function testInsertAfterFirstNode_OneNodeCollectionList() {
        $collection = HashCollectionFactory::createFilledHashCollection(1);
        $this->assertEquals(1, $collection->count());
        $collection->insertAfter(1, 'new hash', 'new hash data');
        $this->assertEquals(2, $collection->count());
        $this->assertEquals(1, $collection->firstHash());
        $this->assertEquals('new hash', $collection->lastHash());
    }

    public function testInsertAfterInvalidHash_ThrowException() {
        try {
            $collection = HashCollectionFactory::createFilledHashCollection(1);
            $collection->insertAfter('invalid hash', 'new hash', 'new hash data');
            throw new \Exception('no errors thrown');
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(HashKeyDoesNotExists::class, $exception);
        }
    }

    public function testInsertAfterInvalidHash_DontThrowException() {

        $collection = HashCollectionFactory::createFilledHashCollection(1);
        $result = $collection->insertAfter('invalid hash', 'new hash', 'new hash data', true,true);
        $this->assertFalse($result);
    }


}
