<?php

namespace AlbertvanKiel\Classification\Tests;

use PHPUnit\Framework\TestCase;
use AlbertvanKiel\Classification\Tokenizer\Tokenizer;

class TokenizerTest extends TestCase
{
    public function test_it_tokenizes_text_and_removes_punctuation(): void
    {
        $tokenizer = new Tokenizer();

        $tokens = $tokenizer->tokenize("Lorem Ipsum Dolor Sit Amet");
        $this->assertEquals(['lorem', 'ipsum', 'dolor', 'sit', 'amet'], $tokens);
    }

    public function test_it_removes_stop_words(): void
    {
        $tokenizer = new Tokenizer();

        $tokens = $tokenizer->tokenize("This is very good");
        $this->assertEquals(['very', 'good'], $tokens);
    }

    public function test_it_generates_bigrams(): void
    {
        $tokenizer = new Tokenizer([], 2);
        $tokens = $tokenizer->tokenize("This is very good");
        $this->assertEquals(['this_is', 'is_very', 'very_good'], $tokens);
    }
}