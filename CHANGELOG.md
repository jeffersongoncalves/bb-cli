# Changelog

All notable changes to this project will be documented in this file.

## [1.3.2] - 2026-09-08

### Bug Fixes

- **deps:** Update guzzlehttp/guzzle to patch security advisories
- **ci:** Publish release as draft until PHAR asset is attached

### CI/CD

- Pin actions to commit SHA, add dependabot cooldown/composer, trim dist archive
- **release:** Generate CHANGELOG.md and release notes with git-cliff

### Dependencies

- **deps:** Bump actions/checkout from 6 to 7
- **deps:** Bump shivammathur/setup-php

### Documentation

- Add Buy Me a Coffee sponsor link

### Miscellaneous Tasks

- Bump guzzlehttp/guzzle and guzzlehttp/psr7 for security advisories
- Add GitHub Sponsors to FUNDING.yml

## [1.3.1] - 2026-07-24

### CI/CD

- Replace split build/changelog/publish-phar workflows with a single release job

## [1.3.0] - 2026-07-22

### Dependencies

- **deps:** Bump actions/cache from 5 to 6

### Features

- **pr:** Update existing open PR instead of creating a duplicate

## [1.2.3] - 2026-06-23

### Dependencies

- **deps:** Bump actions/checkout from 6 to 7

### Features

- Adicionar comando self-update

### Refactor

- Consume shared laravel-zero-* packages

## [1.2.2] - 2026-06-06

### CI/CD

- **build:** Serialize builds with a concurrency group to avoid ref-lock race
- **release:** Use version.txt as the single source of truth for the version

### Dependencies

- **deps:** Bump actions/upload-artifact from 6 to 7
- **deps:** Bump ramsey/composer-install from 3 to 4
- **deps:** Bump dependabot/fetch-metadata from 2.5.0 to 3.0.0
- **deps:** Bump dependabot/fetch-metadata from 3.0.0 to 3.1.0

### Documentation

- Add build badge and CHANGELOG

### Miscellaneous Tasks

- Refresh portfolio banner
- Bump version to v1.2.2

### Other

- Clean up update-changelog workflow: remove unused debug echo steps
- Chain builds after Update Changelog + fix release-tagged rebuild

On the release path, three workflows fan out in parallel: publish-phar,
Update Changelog, and builds. Update Changelog force-pushes CHANGELOG
and version.txt, which raced with builds and caused non-fast-forward
rejections. Worse, the tag created by the release stayed on the commit
that existed before the PHAR was rebuilt, so `composer require` would
pull a PHAR with the previous version baked in.

This rewires build.yml to:

- Run via workflow_run after Update Changelog completes successfully,
  eliminating the race. Regular push on main still triggers.
- Pin ref and commit branch to main on workflow_run invocations
  (github.event.workflow_run.head_branch resolves to the tag name for
  release events and would land the commit on a detached HEAD / fail
  to push).
- Resolve the build version from workflow_run.head_branch when running
  under workflow_run. `git describe --tags --abbrev=0` is unreliable
  once the pre-release tag and current release tag share a commit.
- After the rebuild commit lands, move the release tag to that commit
  so Packagist (and direct git installs) serve the PHAR whose embedded
  version matches the tag.

Validated end-to-end in the git-worktree-cli sibling repo.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
- Delete .github/workflows/dependabot-auto-merge.yml

## [1.2.1] - 2026-02-26

### Bug Fixes

- **api:** Align field names with Bitbucket Cloud API 2.0 spec

### Other

- Add project banner and update README

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>

## [1.2.0] - 2026-02-24

### Bug Fixes

- **build:** Auto-detect version from latest git tag in composer build script
- **ci:** Use git describe to auto-detect version in build workflows
- **auth:** Use Atlassian account email instead of username for API token auth

## [1.1.0] - 2026-02-24

### Bug Fixes

- **ci:** Replace non-existent write-version-to-file action with shell command
- Move runtime deps back to require and add force-autodiscovery to box.json
- **build:** Remove composer-install from box.json to fix PHAR compilation
- Align box.json and composer.json with reference project

### Documentation

- Add Packagist total downloads badge to README

### Features

- Migrate authentication from App Passwords to API Tokens

### Refactor

- Move all deps to require-dev and configure box.json for dev bundling

## [1.0.0] - 2026-02-23

### Bug Fixes

- **test:** Add Prompt::fallbackWhen(true) to SaveCommand test
- **ci:** Bump PHP to 8.4 in workflows using composer install (locked)
- **ci:** Add phar.readonly=Off for PHAR compilation in build workflows
- **build:** Remove main and output from box.json to match BuildCommand expectations
- **ci:** Remove phpstan and rector from test script (not in require-dev)

### CI/CD

- Add workflow_dispatch trigger to PHPStan workflow
- Remove Windows from test matrix

### Documentation

- Update README with project documentation and align composer metadata

### Features

- **ci:** Add CI workflows for testing, linting, building, and deployment
- **build:** Add composer scripts for testing, linting, and refactoring
- **dev:** Add phpstan and rector to require-dev with phpstan config

### Miscellaneous Tasks

- **gitignore:** Remove `/builds` from ignored files
- Add LICENSE, fix tests badge, move deps to require-dev

### Other

- Initial commit: BB CLI - Bitbucket Cloud CLI with Laravel Zero

Modern Bitbucket Cloud CLI built with Laravel Zero featuring:
- 28 commands: auth, pr, pipeline, branch, env, browse
- Service layer architecture with DTOs, Enums, and Traits
- Guzzle HTTP client with Basic Auth and pagination
- 56 Pest tests (unit + feature) with full mocking

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>


