<?php

namespace WabLab\Collection;

use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Collection\Contracts\IHashCollectionSeeker;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;
use WabLab\Collection\Exception\HashKeysMustNotBeMatched;
use WabLab\DoublyLinkedList\Helpers\FreeingNode;
use WabLab\DoublyLinkedList\Helpers\SettingNodeAfter;
use WabLab\DoublyLinkedList\Helpers\SettingNodeBefore;
use WabLab\HashedTree\NodeTree;
use WabLab\HashedTree\Node;

class HashedLinkedListCollection implements IHashCollection
{

    protected NodeTree $rootNodeTree;

    protected ?Node $firstNode = null;

    /**
     * @var IHashCollectionSeeker
     */
    protected ?IHashCollectionSeeker $seeker = null;

    protected ?Node $lastNode = null;

    public function __construct()
    {
        $this->rootNodeTree = new NodeTree('root', null);
    }

    //
    // LEVEL 0
    //
    public function insertFirst(string $hash, $data, bool $ignoreIfExists = false): bool
    {
        if ($this->assertNodeHashNotExists($hash, !$ignoreIfExists)) {
            $node = $this->createNewUnlinkedNode($hash, $data);
            if($this->firstNode) {
                SettingNodeBefore::process($node, $this->firstNode);
            } else {
                $this->lastNode = $node;
            }
            $this->firstNode = $node;
            return true;
        }
        return false;
    }
    
    public function insertLast(string $hash, $data, bool $ignoreIfExists = false): bool
    {
        if ($this->assertNodeHashNotExists($hash, !$ignoreIfExists)) {
            $node = $this->createNewUnlinkedNode($hash, $data);
            if($this->lastNode) {
                SettingNodeAfter::process($this->lastNode, $node);
            } else {
                $this->firstNode = $node;
            }
            $this->lastNode = $node;
            return true;
        }
        return false;
    }

    public function insertAfter(string $afterHash, string $newHash, $data, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHashExists = false):bool
    {
        if ($this->assertNodeHashExists($afterHash, !$ignoreIfFirstNotExists) && $this->assertNodeHashNotExists($newHash, !$ignoreIfNewHashExists)) {
            $node = $this->createNewUnlinkedNode($newHash, $data);
            $afterNode = $this->getNodeByHash($afterHash);
            $this->setNodeAfterAnother($afterNode, $node);
            return true;
        }
        return false;
    }

    public function insertBefore(string $beforeHash, string $newHash, $data, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHashExists = false):bool
    {
        if ($this->assertNodeHashExists($beforeHash, !$ignoreIfFirstNotExists) && $this->assertNodeHashNotExists($newHash, !$ignoreIfNewHashExists)) {
            $node = $this->createNewUnlinkedNode($newHash, $data);
            $beforeNode = $this->getNodeByHash($beforeHash);
            $this->setNodeBeforeAnother($beforeNode, $node);
            return true;
        }
        return false;
    }


    public function moveFirst(string $hashToMove, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHashExists = false):bool
    {
        try {
            return $this->moveBefore($this->firstHash(), $hashToMove, $ignoreIfFirstNotExists, $ignoreIfNewHashExists);
        } catch (HashKeysMustNotBeMatched $exception) {
            return false;
        }
    }

    public function moveLast(string $hashToMove, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHashExists = false):bool
    {
        try {
            return $this->moveAfter($this->lastHash(), $hashToMove, $ignoreIfFirstNotExists, $ignoreIfNewHashExists);
        } catch (HashKeysMustNotBeMatched $exception) {
            return false;
        }
    }

    public function moveAfter(string $afterHash, string $hashToMove, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHashExists = false):bool
    {
        $this->assertHashesDoesNotMatched($afterHash, $hashToMove);
        if ($this->assertNodeHashExists($afterHash, !$ignoreIfFirstNotExists) && $this->assertNodeHashExists($hashToMove, !$ignoreIfNewHashExists)) {
            $node = $this->getNodeByHash($hashToMove);
            $afterNode = $this->getNodeByHash($afterHash);
            $this->freeNode($node);
            $this->setNodeAfterAnother($afterNode, $node);
            return true;
        }
        return false;
    }

    public function moveBefore(string $beforeHash, string $hashToMove, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHashExists = false):bool
    {
        $this->assertHashesDoesNotMatched($beforeHash, $hashToMove);
        if ($this->assertNodeHashExists($beforeHash, !$ignoreIfFirstNotExists) && $this->assertNodeHashExists($hashToMove, !$ignoreIfNewHashExists)) {
            $node = $this->getNodeByHash($hashToMove);
            $beforeNode = $this->getNodeByHash($beforeHash);
            $this->freeNode($node);
            $this->setNodeBeforeAnother($beforeNode, $node);
            return true;
        }
        return false;
    }


    public function update(string $hash, $data, bool $ignoreIfNotExists = false): bool
    {
        if ($this->assertNodeHashExists($hash, !$ignoreIfNotExists)) {
            $this->setNodePayload($hash, $data);
            return true;
        }
        return false;
    }

    public function updateOrInsert(string $hash, $data): bool
    {
        if ($this->assertNodeHashExists($hash, false)) {
            return $this->update($hash, $data);
        } else {
            return $this->insertLast($hash, $data);
        }
    }

    public function delete(string $hash, bool $ignoreIfNotExists = false): bool
    {
        if ($this->assertNodeHashExists($hash, !$ignoreIfNotExists)) {
            $node = $this->getNodeByHash($hash);
            $this->freeNode($node);
            $this->rootNodeTree->removeChild($node->getHash());
            return true;
        }
        return false;
    }

    public function pullOffStack(?string &$hash = null)
    {
        if ($this->lastNode) {
            $hash = $this->lastNode->getHash();
            $value = $this->lastNode->getPayload();
            $this->delete($this->lastNode->getHash());
            return $value;
        }
        return null;
    }

    public function pullOffQueue(?string &$hash = null)
    {
        if ($this->firstNode) {
            $hash = $this->firstNode->getHash();
            $value = $this->firstNode->getPayload();
            $this->delete($this->firstNode->getHash());
            return $value;
        }
        return null;
    }

    public function reHash(string $fromHash, string $toHash): bool
    {
        $node = $this->getNodeByHash($fromHash);
        if($node) {
            return $this->rootNodeTree->rehashChild($fromHash, $toHash);
        }
        return false;
    }


    public function find(string $hash)
    {
        $node = $this->getNodeByHash($hash);
        return $node ? $node->getPayload() : null;
    }

    public function offset(int $index, ?string &$hash = null)
    {
        $hash = $this->offsetHash($index);
        return $hash ? $this->find($hash) : null;
    }

    public function offsetHash(int $index): ?string
    {
        if($index < $this->count()) {
            $seeker = 0;
            foreach ($this->rootNodeTree->yieldChildren() as $child) {
                if ($seeker == $index) {
                    return $child->getHash();
                }
                $seeker++;
            }
        }
        return null;
    }

    public function isset(string $hash): bool
    {
        return $this->rootNodeTree->isChildExists($hash);
    }

    public function first(?string &$hash = null)
    {
        if ($this->firstNode) {
            $hash = $this->firstNode->getHash();
            return $this->firstNode->getPayload();
        }
        return null;
    }

    public function firstHash(): ?string
    {
        if ($this->firstNode) {
            return $this->firstNode->getHash();
        }
        return null;
    }

    public function last(?string &$hash = null)
    {
        if ($this->lastNode) {
            $hash = $this->lastNode->getHash();
            return $this->lastNode->getPayload();
        }
        return null;
    }

    public function lastHash(): ?string
    {
        if ($this->lastNode) {
            return $this->lastNode->getHash();
        }
        return null;
    }

    public function count(): int
    {
        return $this->rootNodeTree->getChildrenCount();
    }

    /**
     * @return \Generator
     */
    public function yieldAll(?string $initialHash = null)
    {
        if($initialHash) {
            $current = $this->getNodeByHash($initialHash);
        } else {
            $current = $this->firstNode;
        }

        while ($current) {
            yield $current->getHash() => $current->getPayload();
            $current = $current->getRight();
        }

        return null;
    }

    /**
     * @return \Generator
     */
    public function reverseYieldAll(?string $initialHash = null)
    {
        if($initialHash) {
            $current = $this->getNodeByHash($initialHash);
        } else {
            $current = $this->lastNode;
        }

        while ($current) {
            yield $current->getHash() => $current->getPayload();
            $current = $current->getLeft();
        }

        return null;
    }

    public function seeker(?string $initHash = null): IHashCollectionSeeker
    {
        if(!$this->seeker) {
            $this->seeker = new HashedCollectionSeeker($this);
        }

        if($initHash) {
            $this->seeker->current($initHash);
        }

        return $this->seeker;
    }

    //
    // LEVEL 1
    //
    protected function assertNodeHashNotExists(string $hash, bool $throwException = true): bool
    {
        if ($this->rootNodeTree->isChildExists($hash)) {
            if ($throwException) {
                throw new HashKeyAlreadyExists();
            }
            return false;
        }
        return true;
    }

    protected function assertNodeHashExists(string $hash, bool $throwException = true): bool
    {
        if (!$this->rootNodeTree->isChildExists($hash)) {
            if ($throwException) {
                throw new HashKeyDoesNotExists();
            }
            return false;
        }
        return true;
    }

    protected function assertHashesDoesNotMatched(string $hash1, string $hash2)
    {
        if($hash1 == $hash2) {
            throw new HashKeysMustNotBeMatched();
        }
    }

    protected function createNewUnlinkedNode(string $hash, $data): Node
    {
        $node = new Node($hash, $data);
        $this->rootNodeTree->setChild($node);
        return $node;
    }


    protected function setNodePayload(string $hash, $data)
    {
        $this->rootNodeTree->getChild($hash)->setPayload($data);
    }

    protected function getNodeByHash(string $hash): ?Node
    {
        return $this->rootNodeTree->getChild($hash);
    }

    protected function setNodeAfterAnother(Node $afterNode, Node $node): void
    {
        if($afterNode->isLast()) {
            $this->lastNode = $node;
        }
        SettingNodeAfter::process($afterNode, $node);
    }

    protected function setNodeBeforeAnother(?Node $beforeNode, Node $node): void
    {
        if ($beforeNode->isFirst()) {
            $this->firstNode = $node;
        }
        SettingNodeBefore::process($node, $beforeNode);
    }

    protected function freeNode(?Node $node)
    {
        if($node->isLast()) {
            $this->lastNode = $node->getLeft();
        }

        if($node->isFirst()) {
            $this->firstNode = $node->getRight();
        }

        FreeingNode::process($node);
    }

}