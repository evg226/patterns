<?php

namespace Model\Repository;

class IdentityMap
{
    private $identityMap = [];
    public function add($obj)
    {
        $key = $this->getGlobalKey(get_class($obj), $obj->getId());
        $this->identityMap[$key] = $obj;
    }
    public function get(string $classname, int $id)
    {
        $key = $this->getGlobalKey($classname, $id);
        if (isset($this->identityMap[$key])) {
            return $this->identityMap[$key];
        }
        return false;
    }
    private function getGlobalKey(string $classname, int $id)
    {
        return sprintf('%s.%d', $classname, $id);
    }
}
