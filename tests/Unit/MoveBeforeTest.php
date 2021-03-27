<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class MoveBeforeTest extends AbstractTestCase
{

    public function testMovingNodeBeforeAnother_TowNodesCollection()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(2);
        $this->assertEquals(2,$collection->count());
        $this->assertEquals(1, $collection->firstHash());
        $this->assertEquals(2, $collection->lastHash());
        $collection->moveBefore($collection->firstHash(), $collection->lastHash());
        $this->assertEquals(2,$collection->count());
        $this->assertEquals(2, $collection->firstHash());
        $this->assertEquals(1, $collection->lastHash());
    }

    public function testMovingNodeBeforeAnother_10NodesCollection()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertEquals(10,$collection->count());
        $this->assertEquals(1, $collection->firstHash());
        $this->assertEquals(10, $collection->lastHash());

        // move first to the last
        $collection->moveBefore($collection->firstHash(), $collection->lastHash());
        $this->assertEquals(10,$collection->count());
        $this->assertEquals(10, $collection->firstHash());
        $this->assertEquals(9, $collection->lastHash());

        // make sure that all hashes has been shifted
        $counter = 8;
        foreach ($collection->yieldAll() as $key => $value) {
            $counter = ($counter+1) % 10;
            $this->assertEquals($counter+1, $key);
        }

        // put 5 after 6
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $collection->moveBefore(5, 6);
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

    public function testMoveBeforeInvalidHash_ThrowException() {
        try {
            $collection = HashCollectionFactory::createFilledHashCollection(1);
            $collection->moveBefore('invalid hash', 'new hash');
            throw new \Exception('no errors thrown');
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(HashKeyDoesNotExists::class, $exception);
        }
    }

    public function testMoveBeforeInvalidHash_DontThrowException() {
        $collection = HashCollectionFactory::createFilledHashCollection(1);
        $result = $collection->moveBefore('invalid hash', 'new hash', true,true);
        $this->assertFalse($result);
    }

}
