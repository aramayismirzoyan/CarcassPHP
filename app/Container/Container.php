<?php

namespace App\Container;

use App\Exceptions\ContainerException;

class Container
{
    private $bindings = [];

    public function set(string $id, callable $factory): void
    {
        $this->bindings[$id] = $factory;
    }

    public function get(string $id)
    {
        if (! isset($this->bindings[$id])) {
            throw new ContainerException("Container does not exist.");
        }

        $factory = $this->bindings[$id];

        return $factory($this);
    }
}
