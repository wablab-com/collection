<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Collection\Exception\HashKeysMustNotBeMatched;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class MoveAfterTest extends AbstractTestCase
{



    public function testMovingNodeAfterAnother_TowNodesCollection()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(2);
        $this->assertEquals(2,$collection->count());
        $this->assertEquals(1, $collection->firstHash());
        $this->assertEquals(2, $collection->lastHash());
        $collection->moveAfter($collection->lastHash(),$collection->firstHash());
        $this->assertEquals(2,$collection->count());
        $this->assertEquals(2, $collection->firstHash());
        $this->assertEquals(1, $collection->lastHash());
    }


    public function testMovingNodeAfterAnother_10NodesCollection()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertEquals(10,$collection->count());
        $this->assertEquals(1, $collection->firstHash());
        $this->assertEquals(10, $collection->lastHash());

        // move first to the last
        $collection->moveAfter($collection->lastHash(), $collection->firstHash());
        $this->assertEquals(10,$collection->count());
        $this->assertEquals(2, $collection->firstHash());
        $this->assertEquals(1, $collection->lastHash());

        // make sure that all hashes has been shifted
        $counter = 0;
        foreach ($collection->yieldAll() as $key => $value) {
            $counter = ($counter+1) % 10;
            $this->assertEquals($counter+1, $key);
        }

        // put 5 after 6
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $collection->moveAfter(6,5);
        $this->assertEquals(10, $collection->count());
        $counter = 0;
        foreach ($collection->yieldAll() as $key => $value) {
            $counter++;
            if($counter == 5) {
                $this->assertEquals(6, $key);
            } elseif($counter == 6) {
                $this->assertEquals(5, $key);
            }
        }
    }

    public function testMoveAfterInvalidHash_ThrowException()
    {
        try {
            $collection = HashCollectionFactory::createFilledHashCollection(1);
            $collection->moveAfter('invalid hash', 'new hash');
            throw new \Exception('no errors thrown');
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(HashKeyDoesNotExists::class, $exception);
        }
    }

    public function testMoveAfterInvalidHash_DontThrowException()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(1);
        $result = $collection->moveAfter('invalid hash', 'new hash', true,true);
        $this->assertFalse($result);
    }

    public function testMoveAfterSameHash()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(1);
        try {
            $collection->moveAfter('1', '1', true,true);
            throw new \Exception('invalid error');
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(HashKeysMustNotBeMatched::class, $exception);
        }
    }


}
