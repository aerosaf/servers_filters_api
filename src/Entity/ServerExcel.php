<?php

namespace App\Entity;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ServerExcel
{
    private $model;
    private $ram;
    private $hdd;
    private $location;
    private $price;
    private $hardDiskType;
    private $storage;
    private $servers;

    // Define constants
    const SSD = 'SSD';
    const SATA = 'SATA2';
    const SAS = 'SAS';

    const GB_IN_TB = 1000;
    const UNIT_GB = 'GB';
    const UNIT_TB = 'TB';

    public function __construct()
    {
        $kernel = new KernelInterface;
        $absolutePath = $kernel->getProjectDir() . '/public/LeaseWeb_servers_filters_assignment.xlsx';
        $spreadsheet = IOFactory::load($absolutePath);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator(2) as $row) {
            $server = new $this;
            $server->setModel($worksheet->getCell('A' . $row->getRowIndex())->getValue());
            $server->setRam($worksheet->getCell('B' . $row->getRowIndex())->getValue());
            $server->setHdd($worksheet->getCell('C' . $row->getRowIndex())->getValue());
            $server->setLocation($worksheet->getCell('D' . $row->getRowIndex())->getValue());
            $server->setPrice($worksheet->getCell('E' . $row->getRowIndex())->getValue());
            $this->servers[] = $server;
        }
    }


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

    public function getStorage(): ?string
    {
        return $this->storage;
    }

    public function setHdd(string $hdd): self
    {
        $this->hdd = $hdd;
        switch ($hdd) {
            case str_contains($hdd, self::SATA):
                $this->hardDiskType = self::SATA;
                $this->setStorage(self::SATA);
              break;
            case str_contains($hdd, self::SSD):
                $this->hardDiskType = self::SSD;
                $this->setStorage(self::SSD);
              break;
            case str_contains($hdd, self::SAS):
                $this->hardDiskType = self::SAS;
                $this->setStorage(self::SAS);
              break;
          }

        return $this;
    }

    public function setStorage($type): self
    {
        // Validate input
        if (!is_string($this->hdd) || !preg_match('/(GB|TB)/', $this->hdd)) {
            throw new InvalidArgumentException('Invalid storage type');
        }
        
        // Extract storage size
        $storage = str_replace($type, '', $this->hdd);
        $storage = explode('x', $storage);
        
        // Calculate storage size
        if (str_contains($storage[1], self::UNIT_GB)) {
            $storage[1] = str_replace(self::UNIT_GB, '', $storage[1]);
            $storageSize = (int) $storage[0] * (int) $storage[1];
        } else if (str_contains($storage[1], self::UNIT_TB)) {
            $storage[1] = str_replace(self::UNIT_TB, '', $storage[1]);
            $storageSize = (int) $storage[0] * (int) $storage[1] * self::GB_IN_TB;
        } else {
            throw new InvalidArgumentException('Invalid storage size');
        }
        
        // Set storage size
        $this->storage = $storageSize;

        return $this;
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

    public function getAll()
    {
       return $this->servers;
    }
}
