<?php

namespace AlbertvanKiel\Classification\Tests;

use PHPUnit\Framework\TestCase;
use AlbertvanKiel\Classification\Classifiers\NaiveBayes;

class NaiveBayesTest extends TestCase
{
    public function test_it_correctly_categorizes_positive_or_negative(): void
    {
        $classifier = new NaiveBayes();

        $samples = ['great', 'good', 'nice', 'awesome', 'bad', 'boring', 'horrible', 'awful'];
        $labels = ['positive', 'positive', 'positive', 'positive', 'negative', 'negative', 'negative', 'negative'];

        $classifier->train($samples, $labels);
        $result = $classifier->predict('This was a great movie. The special effects were good and I had an awesome time. It was a bit boring sometimes though.');

        $this->assertEquals($result, 'positive');
    }

    public function test_it_throws_exception_if_predicting_without_training(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("The classifier has not been trained yet");

        $classifier = new NaiveBayes();
        $classifier->predict("This should fail");
    }

    public function test_it_returns_valid_probabilities(): void
    {
        $classifier = new NaiveBayes();
        $classifier->train(['good', 'bad'], ['positive', 'negative']);
        $probabilities = $classifier->predictProbabilities('good');

        $this->assertArrayHasKey('positive', $probabilities);
        $this->assertArrayHasKey('negative', $probabilities);

        $this->assertGreaterThan($probabilities['negative'], $probabilities['positive']);

        $sum = array_sum($probabilities);
        $this->assertEqualsWithDelta(1.0, $sum, 0.0001);

    }

    public function test_it_resets_state_when_trained_again(): void
    {
        $classifier = new NaiveBayes();

        $classifier->train(['Tokyo', 'Beijing'], ['japan', 'china']);
        $classifier->train(['Berlin', 'Paris'], ['germany', 'france']);

        $result = $classifier->predict('Berlin');

        $this->assertEquals('germany', $result);
    }

    public function test_it_can_save_and_load(): void
    {
        $path = sys_get_temp_dir() . '/test_model.txt';

        $classifierA = new NaiveBayes();
        $classifierA->train(['Tokyo', 'Beijing'], ['japan', 'china']);
        $classifierA->save($path);

        $classifierB = new NaiveBayes();
        $classifierB->load($path);
        $result = $classifierB->predict('Tokyo');

        $this->assertEquals('japan', $result);
    }
}