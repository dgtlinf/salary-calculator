# Changelog

All notable changes to this project will be documented in this file.


---

## v1.0.31 - 2025-10-06

### Release v1.0.31

#### Fixes

- Fixed GitHub Actions changelog workflow permissions.
  Now `stefanzweifel/git-auto-commit-action` can successfully push updates to `CHANGELOG.md` after every tagged release.
- Added explicit `permissions: contents: write` and proper use of `${{ secrets.GITHUB_TOKEN }}` in the CI configuration.
- Improved CI reliability and consistency for future automated releases.


---

#### Notes

- This release contains **no code changes** — only CI/CD improvements.
- The `CHANGELOG.md` will now auto-update and commit correctly after publishing new releases.
- Ensures Packagist and GitHub remain synchronized for all future tags.


---

**Tag:** `v1.0.31`
**Date:** 2025-10-05
**Type:** Maintenance (CI Fix)
**Author:** Digital Infinity (Goran Krgović)
**License:** MIT
[https://github.com/dgtlinf/salary-calculator](https://github.com/dgtlinf/salary-calculator)

## [Unreleased]

### Added

- Base CHANGELOG.md initialized for GitHub Actions automation.

### Changed

- None.

### Fixed

- None.


---
