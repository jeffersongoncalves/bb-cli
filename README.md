<div class="filament-hidden">

![BB CLI](https://raw.githubusercontent.com/jeffersongoncalves/bb-cli/main/art/jeffersongoncalves-bb-cli.png)

</div>

# BB CLI

[![Buy Me A Coffee](https://img.shields.io/badge/Buy%20Me%20A%20Coffee-support-FFDD00?style=flat-square&logo=buy-me-a-coffee&logoColor=black)](https://buymeacoffee.com/jeffersongoncalves)

A modern Bitbucket Cloud CLI built with [Laravel Zero](https://laravel-zero.com/).

<p align="center">
  <a href="https://github.com/jeffersongoncalves/bb-cli/actions"><img src="https://github.com/jeffersongoncalves/bb-cli/actions/workflows/run-tests.yml/badge.svg" alt="Tests" /></a>
  <a href="https://github.com/jeffersongoncalves/bb-cli/actions"><img src="https://github.com/jeffersongoncalves/bb-cli/actions/workflows/build.yml/badge.svg" alt="Build" /></a>
  <a href="https://packagist.org/packages/jeffersongoncalves/bb-cli"><img src="https://img.shields.io/packagist/dt/jeffersongoncalves/bb-cli" alt="Total Downloads" /></a>
  <a href="https://github.com/jeffersongoncalves/bb-cli/blob/main/LICENSE"><img src="https://img.shields.io/github/license/jeffersongoncalves/bb-cli" alt="License" /></a>
  <img src="https://img.shields.io/badge/php-%3E%3D8.2-8892BF" alt="PHP 8.2+" />
</p>

## Features

- **Pull Requests** - Create, list, approve, merge, decline, diff, and manage PRs
- **Pipelines** - Trigger, monitor, and wait for CI/CD pipelines
- **Branches** - List and filter branches by name or author
- **Environments** - Manage deployment environments and variables
- **Authentication** - Secure credential storage with API tokens
- **Browse** - Open repositories in the browser from the terminal
- **Auto-detection** - Automatically detects workspace/repo from git remote

## Requirements

- PHP 8.2+
- Git

## Installation

```bash
composer global require jeffersongoncalves/bb-cli
```

Or clone and build locally:

```bash
git clone https://github.com/jeffersongoncalves/bb-cli.git
cd bb-cli
composer install
php bb app:build bb
```

## Getting Started

### 1. Create a Bitbucket API Token

1. Go to your Bitbucket **Account settings** > **Security** > **[API tokens](https://support.atlassian.com/bitbucket-cloud/docs/api-tokens/)**
2. Click **Create API token with scopes**
3. Name the token (e.g. `bb-cli`) and set an expiry date
4. Select **Bitbucket** as the app and grant the following [permissions](https://support.atlassian.com/bitbucket-cloud/docs/api-token-permissions/):

> **Note:** In Bitbucket API tokens, Write does **not** imply Read — each permission must be granted separately.

| Scope | Permission | Scope ID | Required for |
|-------|-----------|----------|-------------|
| **Repositories** | Read | `read:repository:bitbucket` | List branches, browse source code |
| **Pull Requests** | Read | `read:pullrequest:bitbucket` | View and list PRs, diffs, commits |
| **Pull Requests** | Write | `write:pullrequest:bitbucket` | Create, approve, merge, decline PRs |
| **Pipelines** | Read | `read:pipeline:bitbucket` | View pipeline status and logs |
| **Pipelines** | Write | `write:pipeline:bitbucket` | Trigger and stop pipelines |
| **Pipelines** | Admin | `admin:pipeline:bitbucket` | Manage environment variables |

5. Click **Create token** and **copy it immediately** — it will only be shown once

> **Migrating from App Passwords?** API tokens are the [long-term replacement](https://support.atlassian.com/bitbucket-cloud/docs/api-tokens/) for App Passwords. Simply run `bb auth:save` again with your new API token — existing `config.json` files using the old `app_password` format are still supported.
>
> **Important:** API tokens require your **Atlassian account email** for API authentication, not your Bitbucket username. Your email can be found in Bitbucket under **Personal settings** > **Email Aliases**.

### 2. Save your credentials

```bash
bb auth:save
```

You will be prompted for your Atlassian account email and API token.

### 3. Verify authentication

```bash
bb auth:show
```

### 4. Start using commands

```bash
bb pr:list
bb pipeline:latest
bb browse
```

> All commands auto-detect the repository from your git remote. Use `--project=owner/repo` to override.

## Commands

### Authentication

| Command | Description |
|---------|-------------|
| `auth:save` | Save Bitbucket credentials (email and API token) |
| `auth:show` | Display saved credentials |

### Pull Requests

| Command | Description |
|---------|-------------|
| `pr:list` | List pull requests (filter by `--state` and `--destination`) |
| `pr:create <source> [destination]` | Create a new pull request |
| `pr:approve <id>` | Approve a PR (use `0` to approve all open PRs) |
| `pr:unapprove <id>` | Remove approval from a PR |
| `pr:request-changes <id>` | Request changes on a PR |
| `pr:unrequest-changes <id>` | Remove change request from a PR |
| `pr:merge <id>` | Merge a PR (`--strategy=merge_commit\|squash\|fast_forward`) |
| `pr:decline <id>` | Decline a PR |
| `pr:commits <id>` | List commits in a PR |
| `pr:diff <id>` | Display the diff of a PR |
| `pr:files <id>` | List changed files in a PR |

### Pipelines

| Command | Description |
|---------|-------------|
| `pipeline:latest` | Get the latest pipeline status |
| `pipeline:get <id>` | Get pipeline details by UUID or build number |
| `pipeline:run <branch>` | Trigger a pipeline for a branch |
| `pipeline:custom <branch> <pattern>` | Trigger a custom pipeline |
| `pipeline:wait [id]` | Wait for a pipeline to complete with live status |

### Branches

| Command | Description |
|---------|-------------|
| `branch:list` | List all repository branches |
| `branch:name <pattern>` | Filter branches by name pattern |
| `branch:user <name>` | List branches by a specific author |

### Environments

| Command | Description |
|---------|-------------|
| `env:list` | List deployment environments |
| `env:variables <environment>` | List variables for an environment |
| `env:create-variable <environment>` | Create an environment variable |
| `env:update-variable <environment>` | Update an environment variable |

### Browse

| Command | Description |
|---------|-------------|
| `browse` | Open repository in the browser |
| `browse:show` | Display repository URL |

## Global Options

| Option | Description |
|--------|-------------|
| `--project=owner/repo` | Override auto-detected repository |
| `--help` | Show command help |
| `-v` | Verbose output |

## Development

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run tests only
composer test:unit

# Code formatting
composer lint

# Static analysis
composer phpstan
```

## License

BB CLI is open-source software licensed under the [MIT license](LICENSE).
