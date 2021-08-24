<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation\Config;

class Code
{
    private string $name;

    /** @var Script[]  */
    private array $scripts;

    public function __construct(array $inputArray)
    {
        $this->name = $inputArray['name'];

        $this->scripts = array_map(
            fn($v) => new Script($v),
            $inputArray['script']
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Script[]
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }
}
