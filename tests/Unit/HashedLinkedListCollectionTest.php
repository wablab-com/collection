<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class HashedLinkedListCollectionTest extends AbstractTestCase
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

    public function testInsertIgnoreDuplicateRecord() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $collection->insert('test_hash', 'test data');
        $result = $collection->insert('test_hash', 'test data', true);
        $this->assertFalse($result);
    }




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

    public function testUpdateOrInsert() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $collection->updateOrInsert(1, ['id' => 'updated']);
        $collection->updateOrInsert(500, ['id' => 'inserted']);
        $this->assertEquals('updated', $collection->find(1)['id']);
        $this->assertEquals('inserted', $collection->find(500)['id']);
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

    public function testPullOffStack() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $hash = 0;
        $collection->pullOffStack($hash);
        $this->assertEquals(100, $hash);
    }

    public function testPullOffQueue() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $hash = 0;
        $collection->pullOffQueue($hash);
        $this->assertEquals(1, $hash);
    }

    public function testEmptyCollectionPullOffStack() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $this->assertNull($collection->pullOffStack());
        $this->assertNull($collection->last());
        $this->assertNull($collection->lastHash());
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

    public function testYieldAll() {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $counter = 0;
        foreach($collection->yieldAll() as $hash => $item) {
            $counter++;
            $this->assertEquals($counter, $hash);
        }
        $this->assertEquals(10, $counter);
    }


    public function testYieldAllReverse() {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $counter = 10;
        foreach($collection->reverseYieldAll() as $hash => $item) {
            $this->assertEquals($counter, $hash);
            $counter--;
        }
        $this->assertEquals(0, $counter);
    }

    public function testRehash() {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertTrue($collection->reHash(5, 500));
        $counter = 0;
        foreach($collection->yieldAll() as $hash => $item) {
            $counter++;
            if($counter == 5) {
                $this->assertEquals(500, $hash);
            } else {
                $this->assertEquals($counter, $hash);
            }
        }
        $this->assertEquals(10, $counter);

        $this->assertFalse($collection->reHash('invalid hash', 'anything'));
    }

    public function testIsset() {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertTrue($collection->isset(5));
        $this->assertFalse($collection->isset('invalid key'));
    }

}
