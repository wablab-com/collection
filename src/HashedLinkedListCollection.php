<?php

namespace WabLab\Collection;

use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;
use WabLab\HashedLinkedList\Node;

class HashedLinkedListCollection implements IHashCollection
{
    /**
     * @var Node
     */
    protected Node $rootHashLinkedList;

    /**
     * @var Node
     */
    protected ?Node $firstNode = null;

    /**
     * @var Node
     */
    protected ?Node $lastNode = null;

    public function __construct()
    {
        $this->rootHashLinkedList = new Node('root', null);
    }

    //
    // LEVEL 0
    //
    public function insert(string $hash, $data, bool $ignoreIfExists = false): bool
    {
        if ($this->assertNodeHashNotExists($hash, !$ignoreIfExists)) {
            $node = $this->createNewUnlinkedNode($hash, $data);
            $this->makeItAFirstNodeIfTheSetIsEmpty($node);
            $this->makeItLastNode($node);
            return true;
        }
        return false;
    }

    public function insertAfter(string $afterHash, string $newHash, $data, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHasExists = false)
    {
        if ($this->assertNodeHashExists($afterHash, !$ignoreIfFirstNotExists) && $this->assertNodeHashNotExists($newHash, !$ignoreIfNewHasExists)) {
            $node = $this->createNewUnlinkedNode($newHash, $data);
            $afterNode = $this->getNodeByHash($afterHash);
            $this->setNodeAfterAnother($afterNode, $node);
            return true;
        }
        return false;
    }

    public function insertBefore(string $beforeHash, string $newHash, $data, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHasExists = false)
    {
        if ($this->assertNodeHashExists($beforeHash, !$ignoreIfFirstNotExists) && $this->assertNodeHashNotExists($newHash, !$ignoreIfNewHasExists)) {
            $node = $this->createNewUnlinkedNode($newHash, $data);
            $beforeNode = $this->getNodeByHash($beforeHash);
            $this->setNodeBeforeAnother($beforeNode, $node);
            return true;
        }
        return false;
    }

    public function moveAfter(string $afterHash, string $hashToMove, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHasExists = false)
    {
        if ($this->assertNodeHashExists($afterHash, !$ignoreIfFirstNotExists) && $this->assertNodeHashExists($hashToMove, !$ignoreIfNewHasExists)) {
            $node = $this->getNodeByHash($hashToMove);
            $this->freeNodeFromBetweenNodes($node);
            $afterNode = $this->getNodeByHash($afterHash);
            $this->setNodeAfterAnother($afterNode, $node);
            return true;
        }
        return false;
    }

    public function moveBefore(string $beforeHash, string $hashToMove, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHasExists = false)
    {
        if ($this->assertNodeHashExists($beforeHash, !$ignoreIfFirstNotExists) && $this->assertNodeHashExists($hashToMove, !$ignoreIfNewHasExists)) {
            $node = $this->getNodeByHash($hashToMove);
            $this->freeNodeFromBetweenNodes($node);
            $beforeNode = $this->getNodeByHash($beforeHash);
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
            return $this->insert($hash, $data);
        }
    }

    public function delete(string $hash, bool $ignoreIfNotExists = false): bool
    {
        if ($this->assertNodeHashExists($hash, !$ignoreIfNotExists)) {
            $node = $this->getNodeByHash($hash);
            $this->adjustIfTheFirstNodeDeleted($node);
            $this->adjustIfTheLastNodeDeleted($node);
            $this->deleteNodeThenConnectLeftsAndRights($node);
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
        return $this->rootHashLinkedList->right()->rehash($fromHash, $toHash);
    }


    public function find(string $hash)
    {
        $node = $this->rootHashLinkedList->right()->get($hash);
        return $node ? $node->getPayload() : null;
    }

    public function isset(string $hash): bool
    {
        return $this->rootHashLinkedList->right()->isset($hash);
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
        return $this->rootHashLinkedList->right()->count();
    }

    /**
     * @return \Generator
     */
    public function yieldAll()
    {
        $current = $this->firstNode;
        while ($current) {
            yield $current->getHash() => $current->getPayload();
            $current = $current->right()->first();
        }
    }

    /**
     * @return \Generator
     */
    public function reverseYieldAll()
    {
        $current = $this->lastNode;
        while ($current) {
            yield $current->getHash() => $current->getPayload();
            $current = $current->left()->first();
        }
    }

    //
    // LEVEL 1
    //
    protected function assertNodeHashNotExists(string $hash, bool $throwException = true): bool
    {
        if ($this->rootHashLinkedList->right()->isset($hash)) {
            if ($throwException) {
                throw new HashKeyAlreadyExists();
            }
            return false;
        }
        return true;
    }

    protected function makeItLastNode(Node $node): void
    {
        if($node->right()->first()) {
            throw new \Exception('Node already linked with a right one.');
        }

        if ($this->lastNode && $node->getHash() != $this->lastNode->getHash()) {
            Node::chainNodes($this->lastNode, $node);
        }
        $this->lastNode = $node;
    }

    protected function makeItFirstNode(Node $node): void
    {
        if($node->left()->first()) {
            throw new \Exception('Node already linked with a left one.');
        }
        if ($this->firstNode && $node->getHash() != $this->firstNode->getHash()) {
            Node::chainNodes($node, $this->firstNode);
        }
        $this->firstNode = $node;
    }

    protected function makeItAFirstNodeIfTheSetIsEmpty(Node $node): void
    {
        if (!$this->firstNode) {
            $this->firstNode = $node;
        }
    }

    protected function createNewUnlinkedNode(string $hash, $data): Node
    {
        $node = new Node($hash, $data);
        $this->rootHashLinkedList->right()->set($node);
        return $node;
    }

    protected function assertNodeHashExists(string $hash, bool $throwException = true): bool
    {
        if (!$this->rootHashLinkedList->right()->isset($hash)) {
            if ($throwException) {
                throw new HashKeyDoesNotExists();
            }
            return false;
        }
        return true;
    }

    protected function setNodePayload(string $hash, $data): bool
    {
        return $this->rootHashLinkedList->right()->get($hash)->setPayload($data);
    }

    protected function adjustIfTheFirstNodeDeleted(?Node $node): void
    {
        if ($this->firstNode && $this->firstNode->getHash() == $node->getHash()) {
            $this->firstNode = $node->right()->first();
        }
    }

    protected function adjustIfTheLastNodeDeleted(?Node $node): void
    {
        if ($this->lastNode && $this->lastNode->getHash() == $node->getHash()) {
            $this->lastNode = $node->left()->first();
        }
    }

    protected function deleteNodeThenConnectLeftsAndRights(?Node $node): void
    {
        $this->rootHashLinkedList->right()->unset($node->getHash());
        $node->delete(Node::DELETE_STRATEGY_MERGE);
    }


    protected function getNodeByHash(string $hash): ?Node
    {
        return $this->rootHashLinkedList->right()->get($hash);
    }

    protected function setNodeBetweenNodes(Node $leftNode, Node $rightNode, Node $node): void
    {
        $rightNode->left()->unset($leftNode->getHash());
        $leftNode->right()->unset($rightNode->getHash());
        Node::chainNodes($leftNode, $node);
        Node::chainNodes($node, $rightNode);
    }

    protected function freeNodeFromBetweenNodes(Node $node): void
    {
        $leftNode = $node->left()->first();
        if($leftNode) {
            $leftNode->right()->unset($node->getHash());
            $node->left()->unset($leftNode->getHash());
        }

        $rightNode = $node->right()->first();
        if($rightNode) {
            $rightNode->left()->unset($node->getHash());
            $node->right()->unset($rightNode->getHash());
        }

        if($leftNode && $rightNode) {
            Node::chainNodes($leftNode, $rightNode);
        } elseif($leftNode) {
            $this->lastNode = $leftNode;
        } elseif($rightNode) {
            $this->firstNode = $rightNode;
        }

    }

    protected function setNodeAfterAnother(Node $afterNode, Node $node): void
    {
        $rightNode = $afterNode->right()->first();
        if ($rightNode) {
            $this->setNodeBetweenNodes($afterNode, $rightNode, $node);
        } else {
            $this->makeItLastNode($node);
        }
    }

    protected function setNodeBeforeAnother(?Node $beforeNode, Node $node): void
    {
        $leftNode = $beforeNode->left()->first();
        if ($leftNode) {
            $this->setNodeBetweenNodes($leftNode, $beforeNode, $node);
        } else {
            $this->makeItFirstNode($node);
        }
    }

}