<?php

namespace AlbertvanKiel\Classification\Contracts;

interface Dataset
{
    /**
     * Loads dataset from a file path.
     * 
     * @param string $path The absolute path to the file
     * @return self Returns instance of the dataset object.
     * @throws \RunTimeException If the file cannot be read or loaded.
     */
    public static function fromFile(string $path): self;

    /**
     * Returns array of samples.
     * 
     * @return string[]
     */
    public function getSamples(): array;

    /**
     * Returns array of labels for categorization.
     * 
     * @return string[]
     */
    public function getLabels(): array;
}