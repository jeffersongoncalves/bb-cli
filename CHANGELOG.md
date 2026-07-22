# Changelog

All notable changes to `bb-cli` will be documented in this file.

## [Unreleased](https://github.com/jeffersongoncalves/bb-cli/compare/v1.3.0...main)

### Fixed

- Align field names with Bitbucket Cloud API 2.0 spec

## [v1.3.0](https://github.com/jeffersongoncalves/bb-cli/compare/v1.2.3...v1.3.0) - 2026-07-22

### What's Changed

* chore(deps): bump actions/cache from 5 to 6 by @dependabot[bot] in https://github.com/jeffersongoncalves/bb-cli/pull/6

**Full Changelog**: https://github.com/jeffersongoncalves/bb-cli/compare/v1.2.3...v1.3.0

## [v1.2.3](https://github.com/jeffersongoncalves/bb-cli/compare/v1.2.2...v1.2.3) - 2026-06-23

Add the `self-update` command — update the bb CLI to the latest release directly from the terminal.

## [v1.2.2](https://github.com/jeffersongoncalves/bb-cli/compare/v1.2.0...v1.2.2) - 2026-06-06

Adopt version.txt release flow (version.txt as version source, no tag-move; concurrency on builds).

## [v1.2.0](https://github.com/jeffersongoncalves/bb-cli/compare/v1.1.0...v1.2.0) - 2026-02-24

### Fixed

- Use Atlassian account email instead of username for API token auth
- Use git describe to auto-detect version in build workflows
- Auto-detect version from latest git tag in composer build script

## [v1.1.0](https://github.com/jeffersongoncalves/bb-cli/compare/v1.0.0...v1.1.0) - 2026-02-23

### Added

- Migrate authentication from App Passwords to API Tokens

### Fixed

- Align box.json and composer.json with reference project
- Remove composer-install from box.json to fix PHAR compilation
- Replace non-existent write-version-to-file action with shell command

### Changed

- Move all deps to require-dev and configure box.json for dev bundling

## [v1.0.0](https://github.com/jeffersongoncalves/bb-cli/releases/tag/v1.0.0) - 2026-02-23

### Added

- Pull Requests management (list, create, approve, merge, decline, diff, files, commits)
- Pipelines management (latest, get, run, custom, wait)
- Branches listing and filtering (by name, by author)
- Environments and variables management
- Authentication with API tokens
- Browse repository in browser
- Auto-detection of workspace/repo from git remote
- CI workflows for testing, linting, building, and deployment
- PHPStan static analysis and Rector refactoring
- Composer scripts for testing and linting
