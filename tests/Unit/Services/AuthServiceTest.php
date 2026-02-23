<?php

use App\Services\AuthService;

beforeEach(function () {
    $this->tempDir = sys_get_temp_dir() . '/bb-cli-test-' . uniqid();
    mkdir($this->tempDir, 0700, true);
    $this->configPath = $this->tempDir . '/config.json';

    $this->authService = new AuthService;
    $this->authService->setConfigPath($this->configPath);
});

afterEach(function () {
    if (file_exists($this->configPath)) {
        unlink($this->configPath);
    }
    if (is_dir($this->tempDir)) {
        rmdir($this->tempDir);
    }
});

it('saves credentials to config file', function () {
    $this->authService->save('testuser', 'testpass');

    expect(file_exists($this->configPath))->toBeTrue();

    $data = json_decode(file_get_contents($this->configPath), true);
    expect($data['username'])->toBe('testuser');
    expect($data['app_password'])->toBe('testpass');
});

it('loads saved credentials', function () {
    $this->authService->save('testuser', 'testpass');
    $credentials = $this->authService->load();

    expect($credentials)->not->toBeNull();
    expect($credentials->username)->toBe('testuser');
    expect($credentials->appPassword)->toBe('testpass');
});

it('returns null when config file does not exist', function () {
    $credentials = $this->authService->load();

    expect($credentials)->toBeNull();
});

it('returns null when config file is invalid', function () {
    file_put_contents($this->configPath, 'invalid json');
    $credentials = $this->authService->load();

    expect($credentials)->toBeNull();
});

it('checks authentication status', function () {
    expect($this->authService->isAuthenticated())->toBeFalse();

    $this->authService->save('testuser', 'testpass');

    expect($this->authService->isAuthenticated())->toBeTrue();
});

it('creates directory if not exists', function () {
    $nestedPath = $this->tempDir . '/nested/deep/config.json';
    $this->authService->setConfigPath($nestedPath);
    $this->authService->save('testuser', 'testpass');

    expect(file_exists($nestedPath))->toBeTrue();

    // Cleanup
    unlink($nestedPath);
    rmdir($this->tempDir . '/nested/deep');
    rmdir($this->tempDir . '/nested');
});
