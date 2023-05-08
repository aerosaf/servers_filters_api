<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class ServerFilter
{
    
    public const MIN_STORAGE = 'minStorage';
    public const MAX_STORAGE = 'maxStorage';
    public const RAM = 'ram';
    public const HARD_DISK_TYPE = 'harddisk_type';
    public const LOCATION = 'location';

    /**
     * @Assert\Type("integer")
     * @Assert\GreaterThanOrEqual(0)
     */
    public ?int $minStorage;

    /**
     * @Assert\Type("integer")
     * @Assert\GreaterThanOrEqual(propertyPath="minStorage")
     */
    public ?int $maxStorage;

    /**
     * @Assert\Type("string")
     */
    public ?string $ram;

    /**
     * @Assert\Type("string")
     */
    public ?string $harddiskType;

    /**
     * @Assert\Type("string")
     */
    public ?string $location;

    public function __construct(
        ?int $minStorage = null,
        ?int $maxStorage = null,
        ?string $ram = null,
        ?string $harddiskType = null,
        ?string $location = null
    ) {
        $this->minStorage = $minStorage ?? 0;
        $this->maxStorage = $maxStorage ?? 0;
        $this->ram = $ram ?? '';
        $this->harddiskType = $harddiskType ?? '';
        $this->location = $location ?? '';
    }
}
