<?php

namespace Tree;

use Tree\Exception\TreeException;
use Tree\Exception\NotFoundException;
use Tree\Exception\RootAlreadyExistsException;
use Tree\Persistence\PersistenceInterface;
use Ramsey\Uuid\Uuid;

class TreeService {

    /**
     * @var IdentityMap
     */
    protected $map;

    /**
     * @var PersistenceInterface
     */
    protected $storage;

    public function __construct(PersistenceInterface $storage)
    {
        $this->map =  new IdentityMap();
        $this->storage = $storage;
    }

    public function create(Node $node)
    {
        try {
            $root = $this->getRoot();

            throw new RootAlreadyExistsException();
        } catch (NotFoundException $e) {
            $this->storage->createRoot($node->getId(), $node->getName(), $node->getTitle(), $node->getContent());
            $metadata = new Metadata(1,2,1);

            $this->map->add($node, $metadata);
        }
    }

    /**
     * @return Node
     */
    public function getRoot()
    {
        $raw = $this->storage->getRoot();

        $metadata = new Metadata($raw['left'], $raw['right'], $raw['level']);
        $root = new Node(Uuid::fromString($raw['id']), $raw['name'], $raw['title'], $raw['content']);

        $this->map->add($root, $metadata);

        return $root;
    }

    public function findById(Uuid $id)
    {
        if ($this->map->has($id)) {
            $node = $this->map->getNode($id);
        } else {
            $raw = $this->storage->findById($id->toString());

            $metadata = new Metadata($raw['left'], $raw['right'], $raw['level']);
            $node = new Node(Uuid::fromString($raw['id']), $raw['name'], $raw['title'], $raw['content']);

            $this->map->add($node, $metadata);
        }

        return $node;
    }

    public function findByName($name)
    {
        $raw = $this->storage->findByName($name);

        $metadata = new Metadata($raw['left'], $raw['right'], $raw['level']);
        $node = new Node(Uuid::fromString($raw['id']), $raw['name'], $raw['title'], $raw['content']);

        $this->map->add($node, $metadata);

        return $node;
    }

    public function findByPath($path)
    {
        $raw = $this->storage->findByPath($path);

        $metadata = new Metadata($raw['left'], $raw['right'], $raw['level']);
        $node = new Node(Uuid::fromString($raw['id']), $raw['name'], $raw['title'], $raw['content']);

        $this->map->add($node, $metadata);

        return $node;
    }

    public function parent(Node $node)
    {
        $raw = $this->storage->parent($this->map->getMetadata($node->getId()));

        $metadata = new Metadata($raw['left'], $raw['right'], $raw['level']);
        $node = new Node(Uuid::fromString($raw['id']), $raw['name'], $raw['title'], $raw['content']);

        $this->map->add($node, $metadata);

        return $parentNode;
    }

    public function parents(Node $node)
    {
        $raws = $this->storage->parents($this->map->getMetadata($node->getId()));

        $parents = [];
        foreach ($raws as $raw) {
            $metadata = new Metadata($raw['left'], $raw['right'], $raw['level']);
            $node = new Node(Uuid::fromString($raw['id']), $raw['name'], $raw['title'], $raw['content']);

            $this->map->add($node, $metadata);
            $parents[] = $node;
        }

        return $parents;
    }

    public function children(Node $node)
    {
        $raws = $this->storage->children($this->map->getMetadata($node->getId()));

        $children = [];
        foreach ($raws as $raw) {
            $metadata = new Metadata($raw['left'], $raw['right'], $raw['level']);
            $node = new Node(Uuid::fromString($raw['id']), $raw['name'], $raw['title'], $raw['content']);

            $this->map->add($node, $metadata);
            $children[] = $node;
        }

        return $children;

    }


    public function addChildTo(Node $node, Node $child)
    {
        $this->storage->addChildTo($this->map->getMetadata($node->getId()), $child->getId()->toString(), $child->getName(), $child->getTitle(), $child->getContent());

        return $this->findById($child->getId()); // cache metadata
    }

}
