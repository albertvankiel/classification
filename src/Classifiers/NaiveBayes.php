<?php

namespace AlbertvanKiel\Classification\Classifiers;

use AlbertvanKiel\Classification\Contracts\Classifier;
use AlbertvanKiel\Classification\Contracts\Persistable;
use AlbertvanKiel\Classification\Tokenizer\Tokenizer;

use function log;
use function max;
use function exp;

/**
 * Implementation of the Naive Bayes algorithm.
 * 
 * This implementation is optimized for text classification based on word frequencies.
 * It utilizes Laplace smoothing to handle unknown tokens and natural logarithms to prevent 
 * floating-point underflow.
 * 
 * @link https://en.wikipedia.org/wiki/Naive_Bayes_classifier
 */
class NaiveBayes implements Classifier, Persistable
{
    private int $totalDocuments = 0;
    private array $documentCountByClass = [];
    private array $wordCountsByClass = [];
    private array $totalWordsByClass = [];
    private array $vocabulary = [];

    public function __construct(private ?Tokenizer $tokenizer = null) {
        $this->tokenizer = $tokenizer ?? new Tokenizer();
    }

    public function train(array $samples, array $labels): void
    {
        $this->reset();

        foreach ($samples as $key => $text) 
        {
            $label = $labels[$key];
            $this->totalDocuments++;

            if (!isset($this->documentCountByClass[$label])) 
            {
                $this->documentCountByClass[$label] = 0;
                $this->totalWordsByClass[$label] = 0;
                $this->wordCountsByClass[$label] = [];
            }
            $this->documentCountByClass[$label]++;

            $words = $this->tokenizer->tokenize($text);

            foreach ($words as $word) 
            {
                $this->vocabulary[$word] = TRUE;
                if (!isset($this->wordCountsByClass[$label][$word]))
                {
                    $this->wordCountsByClass[$label][$word] = 0;
                }
                $this->wordCountsByClass[$label][$word]++;
                $this->totalWordsByClass[$label]++;
            }
        }
    }

    public function predict(string $sample): string
    {
        if ($this->totalDocuments === 0)
        {
            throw new \RuntimeException("The classifier has not been trained yet");
        }

        $scores = $this->calculateScores($sample);

        $bestLabel = null;
        $highestScore = -INF;

        foreach ($scores as $label => $score) 
        {
            if ($score > $highestScore) 
            {
                $highestScore = $score;
                $bestLabel = $label;
            }
        }

        return $bestLabel;
    }

    public function predictProbabilities(string $sample): array
    {
        $scores = $this->calculateScores($sample);

        $maxScore = max($scores);

        $probabilities = [];
        $totalSum = 0;

        foreach ($scores as $label => $score) 
        {
            $safeScore = $score - $maxScore;
            $normalNumber = exp($safeScore);
            $probabilities[$label] = $normalNumber;
            $totalSum = $totalSum + $normalNumber;
        }

        foreach ($probabilities as $label => $number)
        {
            $probabilities[$label] = $number / $totalSum;
        }

        return $probabilities;
    }

    public function predictBatch(array $samples): array
    {
        $predictions = [];
        foreach($samples as $sample)
        {
            $predictions[] = $this->predict($sample);
        }

        return $predictions;
    }

     /**
     * Calculates the raw log probabilities for a given sample.
     *
     * This method uses Laplace smoothing to handle unknown tokens and 
     * natural logarithms to prevent floating-point underflow when 
     * multiplying small probabilities.
     *
     * @param string $sample The text to analyze.
     * @return array<string, float> An associative array of labels to their raw log scores.
     */
    private function calculateScores(string $sample): array
    {
        $words = $this->tokenizer->tokenize($sample);
        $scores = [];
        $vocabularySize = count($this->vocabulary);

        foreach ($this->documentCountByClass as $label => $count)
        {  
            $priorProbability = $count / $this->totalDocuments;
            $scores[$label] = log($priorProbability);

            foreach ($words as $word)
            {
                if (!isset($this->vocabulary[$word]))
                {
                    continue;
                }
                $wordCountInClass = $this->wordCountsByClass[$label][$word] ?? 0;
                $likelihood = ($wordCountInClass + 1) / ($this->totalWordsByClass[$label] + $vocabularySize);
                $scores[$label] = $scores[$label] + log($likelihood);
            }
        }

        return $scores;
    }

    public function save(string $path): void
    {
        if ($this->totalDocuments === 0)
        {
            throw new \RuntimeException("Cannot save untrained model");
        }

        file_put_contents($path, serialize($this));
    }

    public function load(string $path): void
    {
        if (!file_exists($path))
        {
            throw new \RuntimeException("File not found");
        }
        $model = unserialize(file_get_contents($path));

        if (!$model instanceof self) 
        {
            throw new \RuntimeException("Invalid model file");
        }
        $this->totalDocuments = $model->totalDocuments;
        $this->documentCountByClass = $model->documentCountByClass;
        $this->wordCountsByClass = $model->wordCountsByClass;
        $this->totalWordsByClass = $model->totalWordsByClass;
        $this->vocabulary = $model->vocabulary;
        
    }

    /**
     * Resets the state of the classifier.
     */
    private function reset(): void
    {
        $this->totalDocuments = 0;
        $this->documentCountByClass = [];
        $this->wordCountsByClass = [];
        $this->totalWordsByClass = [];
        $this->vocabulary = [];
    }
}