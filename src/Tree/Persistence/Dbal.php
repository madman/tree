<?php

namespace Tree\Persistence;

use Tree\Metadata;
use Tree\Exception\TreeException;
use Tree\Exception\NotFoundException;

class Dbal implements PersistenceInterface {

    protected $dbal;
    protected $table;

    public function __construct($dbal, $table = 'tree')
    {
        $this->dbal = $dbal;
        $this->table = $table;
    }

    /**
     * @param  int $id      node id
     * @return mixed
     * @throws NotFoundException
     */
    public function findById($id) 
    {
        $node = $this->dbal->fetchAssoc("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);

        if (!$node) {
            throw new NotFoundException("Запис під номером '{$id}' не знайдено");
        }

        return $node;
    }

    /**
     * @param  Metadata   $node
     * @return mixed
     */
    public function parent(Metadata $matadata)
    {
        $parent = $this->dbal->fetchAssoc("SELECT * FROM {$this->table} WHERE `left` < ? AND `right` > ? and level = ? - 1 ", [$matadata->getLeft(), $matadata->getRight(), $matadata->getLevel()]);

        if (!$parent['id']) {
            throw new NotFoundException("Батьківський елемент не знайдено");
        }

        return $parent;
    }

    /**
     * @param  Metadata   $matadata
     * @return mixed
     */
    public function parents(Metadata $matadata)
    {
        $sql = "SELECT * FROM {$this->table} WHERE `left` < :left and `right` > :right and level <> 1 ORDER BY `left` ";
        $stmt = $this->dbal->executeQuery($sql, ['left' => $matadata->getLeft(), 'right' => $matadata->getRight()]);

        $nodes = [];
        while ($row = $stmt->fetch()) {
            $nodes[] = $row;
        }

        return $nodes;
    }

    /**
     * @param  Metadata   $matadata
     * @return mixed
     */
    public function children(Metadata $matadata)
    {
        $sql = "SELECT * FROM {$this->table} where `left` > :left AND `right` < :right AND `level` = :level + 1 ORDER BY `left`";
        $stmt = $this->dbal->executeQuery($sql, ['left' => $matadata->getLeft(), 'right' => $matadata->getRight(), 'level' => $matadata->getLevel()]);

        $nodes = [];
        while ($row = $stmt->fetch()) {
            $nodes[] = $row;
        }

        return $nodes;
    }

    /**
     * @param  mixed $path              path to node
     * @param  string $startNodeId      id of start node
     * @return mixed
     */
    public function findByPath($path, $startNodeId)
    {
        $parts = explode('/', $path);

        $sql = "";

        $i = 0;
        foreach ($parts as $part) {
            $i++;
            $alias = "m" . $i;
            $prev = "m" . ($i - 1);
            $sql .= " LEFT JOIN {$this->table} $alias ON ($alias.left > $prev.left AND $alias.right < $prev.right AND $alias.level = $prev.level + 1 AND $alias.name = '$part')";
        }
        $sql = "SELECT $alias.* FROM {$this->table} m0" . $sql;
        $sql .= ' WHERE m0.id = ?';

        $node = $this->dbal->fetchAssoc($sql, [$startNodeId]);

        if (!$node['id']) {
            throw new NotFoundException("За шляхом '{$path}' нічого не знайдено");
        }

        return $node;
    }

    public function findByName($name)
    {
        $sql = "SELECT * from {$this->table} WHERE name = :name LIMIT 1";
                   
        $node = $this->dbal->fetchAssoc($sql, ['name' => $name]);

        if (!$node['id']) {
            throw new NotFoundException("По імені '{$name}' нічого не знайдено");
        }

        return $node;
    }

    public function getRoot()
    {
        $root = $this->dbal->fetchAssoc("SELECT * FROM {$this->table} WHERE `left` = 1");

        if (!$root['id']) {
            throw new NotFoundException("Вершина дерева не знайдена");
        }

        return $root;
    }

    /**
     *  Create root node
     *
     * @param  string $name
     * @param  string $title
     * @param  string $text
     * @return void
     */
    public function createRoot($id, $name, $title, $content)
    {
        $this->dbal->executeUpdate("INSERT INTO {$this->table} VALUES (?, ?, ?, ?, 1, 2, 1)", [$id, $name, $title, $content]);
    }

    /**
     * @param  string $id
     * @param  string $newName
     * @param  string $newTitle
     * @param  string $newContent
     * @return void
     */
    public function updateNode($id, $newName, $newTitle, $newContent)
    {
        $this->dbal->executeUpdate("UPDATE {$this->table} SET `name` = ?, `title` = ?, content = ? WHERE `id` = ?", [$newName, $newTitle, $newContent, $id]);
    }

    /**
     * @param Metadata   $matadata
     * @param string $id
     * @param string $name
     * @param string $title
     * @param string $text
     */
    public function addChildTo(Metadata $matadata, $id, $name, $title, $content = '')
    {
        $this->dbal->beginTransaction();
        try {
            $this->dbal->executeUpdate("UPDATE {$this->table} SET `right` = `right` + 2, `left` = IF(`left` > ?, `left` + 2, `left`) WHERE `right` >= ?", [$matadata->getRight(), $matadata->getRight()]);
            $this->dbal->executeUpdate("INSERT INTO {$this->table} SET `id` = ?, `name` = ?, `title` = ?, `content` = ?, `left` = ?, `right` = ? + 1, `level` = ? + 1", [$id, $name, $title, $content, $matadata->getRight(), $matadata->getRight(), $matadata->getLevel()]);
            $this->dbal->commit();

        } catch (\Exception $e) {
            $this->dbal->rollBack();

            throw new TreeException("Трапилося якесь неподобство", 0, $e);
        }
    }

    public function delete(Metadata $matadata)
    {
        $this->dbal->beginTransaction();
        try {
            $this->dbal->executeUpdate("DELETE FROM {$this->table} WHERE `left` >= :left AND `right` <= :right", ['left' => $matadata->getLeft(), 'right' => $matadata->getRight()]);
            $this->dbal->executeUpdate("UPDATE {$this->table} SET `left` = IF(`left` > :left, `left` - (:right - :left + 1), `left`), `right` = `right` - (:right - :left + 1) WHERE `right` > :right", ['left' => $matadata->getLeft(), 'right' => $matadata->getRight()]);
            $this->dbal->commit();
        } catch (\Exception $e) {
            $this->dbal->rollBack();

            throw new TreeException("Трапилося якесь неподобство", 0, $e);
        }
    }

}