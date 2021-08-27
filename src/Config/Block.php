<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation\Config;

class Block
{
    private string $name;

    /** @var Code[] $codes */
    private array $codes;

    public function __construct(array $inputArray)
    {
        $this->name = $inputArray['name'];

        $this->codes = array_map(
            fn($v) => new Code($v),
            $inputArray['codes']
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Code[]
     */
    public function getCodes(): array
    {
        return $this->codes;
    }
}
