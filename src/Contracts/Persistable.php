<?php

namespace AlbertvanKiel\Classification\Contracts;

interface Persistable
{
    /**
     * Stores the model in a serialized format.
     * 
     * @param string $path Path to where the model should be stored, e.g. ./model.txt
     * @throws \RuntimeException If the model is untrained or the file cannot be written.
     */
    public function save(string $path): void;

    /**
     * Load the model from an existing file in serialized format, e.g. ./model.txt.
     * 
     * @param string $path Path of where the existing model is located.
     * @throws \RuntimeException If the file cannot be found or if the model is invalid.
     */
    public function load(string $path): void;
}