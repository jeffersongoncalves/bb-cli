<?php

namespace App\Commands;

use JeffersonGoncalves\LaravelZero\SelfUpdate\PharUpdater;
use JeffersonGoncalves\LaravelZero\SelfUpdate\SelfUpdateCommand as BaseSelfUpdateCommand;

class SelfUpdateCommand extends BaseSelfUpdateCommand
{
    protected $description = 'Update the bb CLI to the latest version';

    protected function githubRepo(): string
    {
        return 'jeffersongoncalves/bb-cli';
    }

    protected function assetName(): string
    {
        return 'bb.phar';
    }

    protected function tempPrefix(): string
    {
        return 'bb_';
    }

    protected function currentVersion(): string
    {
        return (string) config('app.version', 'unreleased');
    }

    protected function makeUpdater(): PharUpdater
    {
        return $this->getLaravel()->make(PharUpdater::class);
    }
}
