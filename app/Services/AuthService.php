<?php

namespace App\Services;

use App\DTOs\Credentials;

class AuthService
{
    protected string $configPath;

    public function __construct()
    {
        $home = PHP_OS_FAMILY === 'Windows'
            ? ($_SERVER['USERPROFILE'] ?? $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'])
            : ($_SERVER['HOME'] ?? '~');

        $this->configPath = rtrim($home, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.bb-cli' . DIRECTORY_SEPARATOR . 'config.json';
    }

    public function save(string $username, string $appPassword): void
    {
        $dir = dirname($this->configPath);

        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        $credentials = new Credentials($username, $appPassword);
        file_put_contents($this->configPath, json_encode($credentials->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        chmod($this->configPath, 0600);
    }

    public function load(): ?Credentials
    {
        if (! file_exists($this->configPath)) {
            return null;
        }

        $data = json_decode(file_get_contents($this->configPath), true);

        if (! $data || ! isset($data['username'], $data['app_password'])) {
            return null;
        }

        return Credentials::fromArray($data);
    }

    public function isAuthenticated(): bool
    {
        return $this->load() !== null;
    }

    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    public function setConfigPath(string $path): void
    {
        $this->configPath = $path;
    }
}
