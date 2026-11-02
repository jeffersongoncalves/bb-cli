<?php

use Illuminate\Support\Sleep;
use Tests\TestCase;

// Pipeline polling waits via Sleep — never really sleep in tests.
uses(TestCase::class)->beforeEach(fn () => Sleep::fake())->in('Feature', 'Unit');
