<?php

namespace App\Services;

use App\DTOs\Credentials;
use App\Exceptions\AuthenticationException;
use App\Exceptions\BitbucketApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class BitbucketService
{
    protected const BASE_URL = 'https://api.bitbucket.org/2.0';

    protected Client $client;

    protected Credentials $credentials;

    public function __construct(
        protected AuthService $authService,
    ) {
        $credentials = $this->authService->load();
        if (! $credentials) {
            throw new AuthenticationException;
        }

        $this->credentials = $credentials;
        $this->client = new Client([
            'base_uri' => self::BASE_URL . '/',
            'auth' => [$this->credentials->username, $this->credentials->appPassword],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function get(string $workspace, string $repoSlug, string $endpoint, array $query = []): array
    {
        return $this->request('GET', "repositories/{$workspace}/{$repoSlug}/{$endpoint}", $query);
    }

    public function post(string $workspace, string $repoSlug, string $endpoint, array $data = []): array
    {
        return $this->requestWithBody('POST', "repositories/{$workspace}/{$repoSlug}/{$endpoint}", $data);
    }

    public function put(string $workspace, string $repoSlug, string $endpoint, array $data = []): array
    {
        return $this->requestWithBody('PUT', "repositories/{$workspace}/{$repoSlug}/{$endpoint}", $data);
    }

    public function delete(string $workspace, string $repoSlug, string $endpoint): array
    {
        return $this->request('DELETE', "repositories/{$workspace}/{$repoSlug}/{$endpoint}");
    }

    public function getRaw(string $workspace, string $repoSlug, string $endpoint): string
    {
        try {
            $response = $this->client->request('GET', "repositories/{$workspace}/{$repoSlug}/{$endpoint}", [
                'headers' => ['Accept' => 'text/plain'],
            ]);

            return $response->getBody()->getContents();
        } catch (ClientException $e) {
            $this->handleClientException($e);
        } catch (GuzzleException $e) {
            throw new BitbucketApiException("HTTP request failed: {$e->getMessage()}");
        }
    }

    public function paginate(string $workspace, string $repoSlug, string $endpoint, array $query = []): array
    {
        $results = [];
        $url = "repositories/{$workspace}/{$repoSlug}/{$endpoint}";

        do {
            $response = $this->request('GET', $url, $query);
            $results = array_merge($results, $response['values'] ?? []);
            $url = $response['next'] ?? null;
            $query = []; // next URL already contains query params
        } while ($url);

        return $results;
    }

    public function getCurrentUser(): array
    {
        return $this->request('GET', 'user');
    }

    protected function request(string $method, string $uri, array $query = []): array
    {
        try {
            $options = [];
            if (! empty($query)) {
                $options['query'] = $query;
            }

            $response = $this->client->request($method, $uri, $options);
            $body = $response->getBody()->getContents();

            if (empty($body)) {
                return [];
            }

            return json_decode($body, true) ?? [];
        } catch (ClientException $e) {
            $this->handleClientException($e);
        } catch (GuzzleException $e) {
            throw new BitbucketApiException("HTTP request failed: {$e->getMessage()}");
        }
    }

    protected function requestWithBody(string $method, string $uri, array $data = []): array
    {
        try {
            $options = [];
            if (! empty($data)) {
                $options['json'] = $data;
            }

            $response = $this->client->request($method, $uri, $options);
            $body = $response->getBody()->getContents();

            if (empty($body)) {
                return [];
            }

            return json_decode($body, true) ?? [];
        } catch (ClientException $e) {
            $this->handleClientException($e);
        } catch (GuzzleException $e) {
            throw new BitbucketApiException("HTTP request failed: {$e->getMessage()}");
        }
    }

    protected function handleClientException(ClientException $e): never
    {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents(), true) ?? [];

        if ($statusCode === 401) {
            throw new AuthenticationException('Invalid credentials. Check your username and app password.');
        }

        throw BitbucketApiException::fromResponse($statusCode, $body);
    }
}
