<?php

namespace AlbertvanKiel\Classification\Contracts;

interface Classifier 
{
    /**
     * Train the algorithm.
     * 
     * @param array $samples    Sample sentences or words.
     * @param array $labels     Labels that should match the order of the samples.
     * @return void
     */
    public function train(array $samples, array $labels): void;

    /**
     * Predict the category for a single sample.
     * 
     * @param string $sample    Sample sentence for categorization.
     * @throws RuntimeException Throws exception if model has not been trained yet.
     */
    public function predict(string $sample): string;

    /**
     * Predict the probability in percentages according to each category.
     * 
     * @param string $sample    Sample sentence for categorization.
     * @return array            Percentages of likelihood per category.
     */
    public function predictProbabilities(string $sample): array;

    /**
     * Perform the prediction for multiple samples.
     * 
     * @param array $samples    Collection of samples.
     * @return array            Prediction per sample.
     */
    public function predictBatch(array $samples): array;
}