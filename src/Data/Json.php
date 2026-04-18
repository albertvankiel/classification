<?php

namespace AlbertvanKiel\Classification\Data;

use AlbertvanKiel\Classification\Contracts\Dataset;

use function file_exists;
use function file_get_contents;
use function json_decode;
use function array_column;
use function count;

class Json implements Dataset
{
    private readonly array $samples;
    private readonly array $labels;

    public function __construct(array $samples, array $labels)
    {
        $this->samples = $samples;
        $this->labels = $labels;
    }

    public static function fromFile(string $path): self 
    {
        if (!file_exists($path))
        {
            throw new \RuntimeException("File could not be loaded");
        }

        $json = file_get_contents($path);
        $data = json_decode($json);

        $samples = array_column($data, 'text');
        $labels = array_column($data, 'label');

        if (count($samples) !== count($labels))
        {
            throw new \RuntimeException("Mismatch between samples and labels");
        }

        return new self($samples, $labels);
    }

    public function getSamples(): array
    {
        return $this->samples;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }
}