# Classification
[![Tests](https://github.com/albertvankiel/classification/actions/workflows/tests.yml/badge.svg)](https://github.com/albertvankiel/classification/actions/workflows/tests.yml)

Lightweight zero-dependency text classification library for PHP 8.1+.

This package provides an implementation of the Naive Bayes algorithm for text classification, sentiment analysis and spam filtering.
It includes built-in support for N-Grams, stop-word filtering and Laplace smoothing.

## Installation
Install the package through Composer.
```bash
composer require albertvankiel/classification
```

## Basic usage
The most common use for Naive Bayes is binary classification, such as detecting spam messages or sentiment analysis.
### Training the model
To train the model, you must provide an array of text samples and an array of corresponding labels:
```php
use AlbertvanKiel\Classification\Classifiers\NaiveBayes;

$classifier = new NaiveBayes();

// 1. Prepare your training data
$samples = [
    "Win a FREE iPhone today! Click here to claim your prize.",
    "Cheap medication for sale, limited time offer!",
    "Hey John, are we still on for the marketing meeting at 10?",
    "Can you please send me the Q3 financial report by Friday?"
];
$labels = [
    "spam",
    "spam",
    "not_spam",
    "not_spam"
];

// 2. Train the classifier
$classifier->train($samples, $labels);
```
### Making predictions
Once trained, you can use the classifier to predict the category of text:
```php
// Predict the single most likely category
$prediction = $classifier->predict("Click here to get your free gift card!");
echo $prediction; // Outputs: 'spam'

// Get the exact probability percentages
$probabilities = $classifier->predictProbabilities("Are we meeting tomorrow?");
print_r($probabilities); 
// Outputs: ['not_spam' => 0.98, 'spam' => 0.02]
```
## Loading training datasets from JSON
For loading larger datasets with training data from a JSON file you can use the `Dataset` factory. The JSON file should be an array of
objects containing a `text` and `label` key, for example:

**`dataset.json`**
```json
[
  {
    "text": "Win a FREE iPhone today! Click here to claim your prize.",
    "label": "spam"
  },
  {
    "text": "Hey John, are we still on for the marketing meeting at 10?",
    "label": "not_spam"
  },
  {
    "text": "Cheap medication for sale, limited time offer!",
    "label": "spam"
  }
]
```

Loading the dataset in PHP:
```php
use AlbertvanKiel\Classification\Data\Json;

// Load the data from the file
$dataset = Json::fromFile('/path/to/dataset.json');

// Extract the data and train the classifier
$classifier->train($dataset->getSamples(), $dataset->getLabels());
```
## Saving and loading models
You can train the model once and then save it to a disk and then load it later:
```php
// Save the trained math to a file

$classifier->save('/path/to/storage/spam_model.txt');

// Later, load it without training
$fastClassifier = new NaiveBayes();
$fastClassifier->load('/path/to/storage/spam_model.txt');
$result = $fastClassifier->predict($_POST['message']);
```
## Customizing the tokenizer
By default, the built-in tokenizer filters out common English stop words (such as "the", "and", "is") and uses Unigrams (single words).

You can inject a custom tokenizer for supporting different languages or use N-Grams to give the algorithm context about word order:
```php
use AlbertvanKiel\Classification\Tokenizer\Tokenizer;
use AlbertvanKiel\Classification\Tokenizer\StopWords;

// Example 1: Use Spanish stop words
$spanishStopWords = ['el', 'la', 'los', 'las', 'un', 'una', 'y', 'o', 'pero'];
$spanishTokenizer = new Tokenizer($spanishStopWords);

// Example 2: Use Bigrams (pairs of words) for better context
// "not good" becomes "not_good" instead of ["not", "good"]
$bigramTokenizer = new Tokenizer(StopWords::english(), 2);

// Example 3: Disable stop word filtering entirely
$rawTokenizer = new Tokenizer([]);

// Inject the custom tokenizer into the classifier
$classifier = new NaiveBayes($bigramTokenizer);
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
