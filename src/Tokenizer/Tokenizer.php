<?php

namespace AlbertvanKiel\Classification\Tokenizer;

use AlbertvanKiel\Classification\Tokenizer\StopWords;

class Tokenizer 
{
    private readonly array $stopWords;
    private readonly int $n;

    public function __construct(?array $stopWords = null, $n = 1)
    {
        $this->stopWords = $stopWords ?? StopWords::english();
        if ($n < 1) 
        {
            throw new \InvalidArgumentException("N must be 1 or greater");
        }
        $this->n = $n;
    }

    /**
     * Tokenize a string into an array of words. 
     * Normalizes text by converting it to lowercase and stripping all
     * punctuation and splitting the result by spaces and removing the stopwords.
     * 
     * @param string $text The raw text to tokenize.
     * @return string[] An array of cleaned work tokens.
     */
    public function tokenize(string $text): array
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        $words = explode(' ', $text);
        $stopWordsRemoved = array_diff($words, $this->stopWords);

        $filtered = array_values($stopWordsRemoved);

        return $this->generateNGrams($filtered);
    } 

    /**
     * Converts an array of single words into an array of N-Grams.
     * 
     * @param string[] $words The array of single words.
     * @return string[] The array of N-Grams joined by underscores.
     */
    private function generateNGrams(array $words): array
    {
        if ($this->n === 1)
        {
            return $words;
        }

        $nGrams = [];
        $wordCount = count($words);

        for($i = 0; $i <= ($wordCount - $this->n); $i++)
        {
            $slice = array_slice($words, $i, $this->n);
            $nGrams[] = implode("_", $slice);
        }

        return $nGrams;
    }

}