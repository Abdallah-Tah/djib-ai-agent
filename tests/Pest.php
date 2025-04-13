<?php

use Pest\Laravel\assertDatabaseCount;
use Pest\Laravel\get;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide here will be executed Pest applies it to your
| test cases. This allows you to bind important services and traits
| to your test cases globally, saving you time and repetition.
|
*/

// Apply the base TestCase to all tests in the 'Unit' and 'Feature' directories
uses(Djib\AiAgent\Tests\TestCase::class)->in('Unit', 'Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet
| certain conditions. Pest provides a fluent API for writing assertions
| expectations. You can extend Pest with your own expectations specific
| to your application needs.
|
*/

// expect()->extend('toBeOne', function () {
//     return $this->toBe(1);
// });

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing
| helpers that you use frequently. You can add your own custom logic
| to Pest to help you dry up your test suites. Functions defined here
| will be available globally in your tests.
|
*/

// function something()
// {
//     // ..
// }
