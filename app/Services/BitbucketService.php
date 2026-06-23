<?php

namespace App\Services;

use App\DTOs\Credentials;
use App\Exceptions\AuthenticationException;
use App\Exceptions\BitbucketApiException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use JeffersonGoncalves\LaravelZero\ApiClient\AbstractApiClient;
use JeffersonGoncalves\LaravelZero\ApiClient\ApiException;
use JeffersonGoncalves\LaravelZero\ApiClient\Auth;

class BitbucketService extends AbstractApiClient
{
    protected const BASE_URL = 'https://api.bitbucket.org/2.0';

    protected Credentials $credentials;

    public function __construct(
        protected AuthService $authService,
    ) {
        $credentials = $this->authService->load();
        if (! $credentials) {
            throw new AuthenticationException;
        }

        $this->credentials = $credentials;

        parent::__construct(
            self::BASE_URL,
            Auth::basic($credentials->username, $credentials->apiToken),
        );
    }

    public function getRepo(string $workspace, string $repoSlug, string $endpoint, array $query = []): array
    {
        return $this->get($this->repoPath($workspace, $repoSlug, $endpoint), $query);
    }

    public function postRepo(string $workspace, string $repoSlug, string $endpoint, array $data = []): array
    {
        return $this->post($this->repoPath($workspace, $repoSlug, $endpoint), $data);
    }

    public function putRepo(string $workspace, string $repoSlug, string $endpoint, array $data = []): array
    {
        return $this->put($this->repoPath($workspace, $repoSlug, $endpoint), $data);
    }

    public function deleteRepo(string $workspace, string $repoSlug, string $endpoint): array
    {
        return $this->delete($this->repoPath($workspace, $repoSlug, $endpoint));
    }

    public function getRaw(string $workspace, string $repoSlug, string $endpoint): string
    {
        try {
            $response = $this->client->request('GET', $this->repoPath($workspace, $repoSlug, $endpoint), [
                'headers' => array_merge($this->authHeaders(), ['Accept' => 'text/plain']),
            ]);

            return $response->getBody()->getContents();
        } catch (BadResponseException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body = json_decode($e->getResponse()->getBody()->getContents(), true) ?? [];

            throw $this->newApiException($statusCode, $body);
        } catch (GuzzleException $e) {
            throw $this->newApiException(0, ['message' => "HTTP request failed: {$e->getMessage()}"]);
        }
    }

    /**
     * Walk a cursor-paginated Bitbucket endpoint, aggregating every item.
     *
     * Bitbucket returns items under "values" and an opaque absolute "next"
     * URL for the following page.
     */
    public function paginateRepo(string $workspace, string $repoSlug, string $endpoint, array $query = []): array
    {
        return $this->paginate(
            $this->repoPath($workspace, $repoSlug, $endpoint),
            $query,
            fn (array $response): array => $response['values'] ?? [],
            fn (array $response): ?array => isset($response['next']) && is_string($response['next'])
                ? ['path' => $response['next'], 'query' => []]
                : null,
        );
    }

    public function getCurrentUser(): array
    {
        return $this->get('user');
    }

    protected function newApiException(int $statusCode, array $body): ApiException
    {
        if ($statusCode === 401) {
            throw new AuthenticationException('Invalid credentials. Check your email and API token. API tokens require your Atlassian account email, not your Bitbucket username.');
        }

        return BitbucketApiException::fromResponse($statusCode, $body);
    }

    protected function repoPath(string $workspace, string $repoSlug, string $endpoint): string
    {
        return "repositories/{$workspace}/{$repoSlug}/{$endpoint}";
    }
}
