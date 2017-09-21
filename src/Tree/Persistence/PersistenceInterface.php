<?php

namespace Tree\Persistence;

interface PersistenceInterface {

    public function findById($id);
    public function parent(Metadata $matadata);
    public function parents(Metadata $matadata);
    public function children(Metadata $matadata);
    public function findByPath($path);
    public function findByName($name);
    public function getRoot();
    public function createRoot($id, $name, $title, $content);
    public function updateNode($id, $newName, $newTitle, $newContent);
    public function addChildTo(Metadata $matadata, $name, $title, $content = '');
    public function delete(Metadata $matadata);
}
