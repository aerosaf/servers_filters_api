<?php

namespace App\Entity;

class ServerFilter
{
    public $minStorage;
    public $maxStorage;
    public $ram;
    public $harddiskType;
    public $location;

    public function __construct($minStorage, $maxStorage, $ram, $harddiskType, $location)
    {
        $this->minStorage = $minStorage;
        $this->maxStorage = $maxStorage;
        $this->ram = $ram;
        $this->harddiskType = $harddiskType;
        $this->location = $location;
    }

}