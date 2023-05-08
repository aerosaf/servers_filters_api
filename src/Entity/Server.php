<?php

declare(strict_types=1);

namespace App\Entity;

use InvalidArgumentException;

/**
 * Class Server
 */
class Server
{
    private string $model;
    private string $ram;
    private string $hdd;
    private string $location;
    private string $price;
    private string $hardDiskType;
    private int $storage;
    private array $servers = [];

    // Define constants
    private const SSD = 'SSD';
    private const SATA = 'SATA2';
    private const SAS = 'SAS';

    private const GB_IN_TB = 1000;
    private const UNIT_GB = 'GB';
    private const UNIT_TB = 'TB';

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getRam(): ?string
    {
        return $this->ram;
    }

    public function setRam(string $ram): self
    {
        $this->ram = $ram;

        return $this;
    }

    public function getHdd(): ?string
    {
        return $this->hdd;
    }

    public function getHardDiskType(): ?string
    {
        return $this->hardDiskType;
    }

    public function getStorage(): ?int
    {
        return $this->storage;
    }

    public function setHdd(string $hdd): self
    {
        $this->hdd = $hdd;
        $this->setHardDiskTypeAndStorage($hdd);

        return $this;
    }

    private function setHardDiskTypeAndStorage(string $hdd): void
    {
        if (str_contains($hdd, self::SATA)) {
            $this->hardDiskType = self::SATA;
        } elseif (str_contains($hdd, self::SSD)) {
            $this->hardDiskType = self::SSD;
        } elseif (str_contains($hdd, self::SAS)) {
            $this->hardDiskType = self::SAS;
        } else {
            throw new InvalidArgumentException('Invalid hard disk type');
        }

        $this->setStorage($this->hardDiskType);
    }

    private function setStorage(string $type): void
    {
        if (!is_string($this->hdd) || !preg_match('/(GB|TB)/', $this->hdd)) {
            throw new InvalidArgumentException('Invalid storage type');
        }

        $storage = str_replace($type, '', $this->hdd);
        $storage = explode('x', $storage);

        if (str_contains($storage[1], self::UNIT_GB)) {
            $storage[1] = str_replace(self::UNIT_GB, '', $storage[1]);
            $storageSize = (int)$storage[0] * (int)$storage[1];
        } elseif (str_contains($storage[1], self::UNIT_TB)) {
            $storage[1] = str_replace(self::UNIT_TB, '', $storage[1]);
            $storageSize = (int)$storage[0] * (int)$storage[1] * self::GB_IN_TB;
        } else {
            throw new InvalidArgumentException('Invalid storage size');
        }

        $this->storage = $storageSize;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAll(): array
    {
        return $this->servers;
    }
}