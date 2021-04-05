<?php

use WabLab\Collection\HashedLinkedListCollection;

require __DIR__.'/../vendor/autoload.php';


$recordsCount = 1000000;
$output = [];
$output[] = '------------------------------------------------------';
$output[] = "Testing {$recordsCount} records";
$output[] = '------------------------------------------------------';


//
// Array
//
$startTime = microtime(true);
$collection = [];
for ($i = 0; $i < $recordsCount; $i++) {
    $collection["key {$i}"] = "value {$i}";
}
$execTime = microtime(true) - $startTime;
$output[] = "Array Collection Exec Time: {$execTime} seconds";


//
// Collection
//

//
// insertFirst
//
$startTime = microtime(true);
$collection = new HashedLinkedListCollection();
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->insertFirst("key {$i}", "value {$i}");
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'insertFirst' Exec Time: {$execTime} seconds";


//
// pullOffStack
//
$startTime = microtime(true);
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->pullOffStack();
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'pullOffStack' Exec Time: {$execTime} seconds";


//
// insertLast
//
$startTime = microtime(true);
$collection = new HashedLinkedListCollection();
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->insertLast("key {$i}", "value {$i}");
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'insertLast' Exec Time: {$execTime} seconds";


//
// pullOffQueue
//
$startTime = microtime(true);
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->pullOffQueue();
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'pullOffQueue' Exec Time: {$execTime} seconds";

//
// insertAfter
//
$startTime = microtime(true);
$collection = new HashedLinkedListCollection();
$collection->insertFirst('first', 'first');
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->insertAfter('first', "key {$i}", "value {$i}");
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'insertAfter' Exec Time: {$execTime} seconds";


//
// insertBefore
//
$startTime = microtime(true);
$collection = new HashedLinkedListCollection();
$collection->insertFirst('first', 'first');
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->insertBefore('first', "key {$i}", "value {$i}");
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'insertBefore' Exec Time: {$execTime} seconds";


//
// moveFirst
//
$startTime = microtime(true);
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->moveFirst("key {$i}");
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'moveFirst' Exec Time: {$execTime} seconds";


//
// moveLast
//
$startTime = microtime(true);
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->moveLast("key {$i}");
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'moveLast' Exec Time: {$execTime} seconds";


//
// update
//
$startTime = microtime(true);
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->update("key {$i}", "new value {$i}");
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'update' Exec Time: {$execTime} seconds";


//
// reHash
//
$startTime = microtime(true);
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->reHash("key {$i}", "key2 {$i}");
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'reHash' Exec Time: {$execTime} seconds";


//
// delete
//
$startTime = microtime(true);
for ($i = 0; $i < $recordsCount; $i++) {
    $collection->delete("key2 {$i}");
}
$execTime = microtime(true) - $startTime;
$output[] = "HashedLinkedListCollection 'delete' Exec Time: {$execTime} seconds";


echo implode("\n", $output)."\n";