<?php

use App\DTOs\Credentials;
use App\Services\AuthService;

beforeEach(function () {
    $this->tempDir = sys_get_temp_dir().'/bb-cli-test-'.uniqid();
    $this->originalHome = $_SERVER['HOME'] ?? null;
    $_SERVER['HOME'] = $this->tempDir;
    $this->authService = new AuthService;
});

afterEach(function () {
    $configPath = $this->tempDir.'/.bb-cli/config.json';
    if (file_exists($configPath)) {
        unlink($configPath);
    }
    if (is_dir($this->tempDir.'/.bb-cli')) {
        rmdir($this->tempDir.'/.bb-cli');
    }
    if (is_dir($this->tempDir)) {
        rmdir($this->tempDir);
    }
    if ($this->originalHome === null) {
        unset($_SERVER['HOME']);
    } else {
        $_SERVER['HOME'] = $this->originalHome;
    }
});

it('saves credentials to config file', function () {
    $this->authService->save(new Credentials('testuser', 'testpass'));

    expect(file_exists($this->authService->getConfigPath()))->toBeTrue();

    $data = json_decode(file_get_contents($this->authService->getConfigPath()), true);
    expect($data['username'])->toBe('testuser');
    expect($data['api_token'])->toBe('testpass');
});

it('loads saved credentials', function () {
    $this->authService->save(new Credentials('testuser', 'testpass'));
    $credentials = $this->authService->load();

    expect($credentials)->not->toBeNull();
    expect($credentials->username)->toBe('testuser');
    expect($credentials->apiToken)->toBe('testpass');
});

it('loads legacy app_password format', function () {
    @mkdir($this->tempDir.'/.bb-cli', 0700, true);
    file_put_contents($this->authService->getConfigPath(), json_encode([
        'username' => 'testuser',
        'app_password' => 'legacy-pass',
    ]));

    $credentials = $this->authService->load();

    expect($credentials)->not->toBeNull();
    expect($credentials->username)->toBe('testuser');
    expect($credentials->apiToken)->toBe('legacy-pass');
});

it('returns null when config file does not exist', function () {
    expect($this->authService->load())->toBeNull();
});

it('returns null when config file is invalid', function () {
    @mkdir($this->tempDir.'/.bb-cli', 0700, true);
    file_put_contents($this->authService->getConfigPath(), 'invalid json');

    expect($this->authService->load())->toBeNull();
});

it('checks authentication status', function () {
    expect($this->authService->isAuthenticated())->toBeFalse();

    $this->authService->save(new Credentials('testuser', 'testpass'));

    expect($this->authService->isAuthenticated())->toBeTrue();
});

it('creates directory if not exists', function () {
    expect(is_dir($this->tempDir.'/.bb-cli'))->toBeFalse();

    $this->authService->save(new Credentials('testuser', 'testpass'));

    expect(is_dir($this->tempDir.'/.bb-cli'))->toBeTrue();
});

it('returns config path', function () {
    $path = $this->authService->getConfigPath();

    expect($path)->toContain('.bb-cli')
        ->and($path)->toContain('config.json');
});
