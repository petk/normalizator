# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - YYYY-MM-DD

### Fixed

- Docker tag format fixed.

### Added

- Encoding can be manually entered when cannot be confidently retrieved
  automatically.

## [0.0.2] - 2023-06-19

### Fixed

- Trimming EOLs in empty files.
- Script execution from the vendor directory (locating autoload.php file).
- Reading CRLF files from the Git attributes.
- Improved filename resolving in case of existing files before saving.

### Added

- Normalizator requirements are now checked before running the script.
- Dependency injection container.
- Option `--path-name` refactored to `--name` and `--extension`.
- PSR-14 event dispatcher.
- Extended list of trailing whitespace characters trimmed.
- Simple cache layer to improve performance.
- Fix command asks user for confirmation to continue.
- Self update command to update normalizator.phar to its latest version from
  GitHub releases.
- Footer report with number of processed files, script execution time and memory
  consumption.
- Docker image.

## [0.0.1] - 2023-06-12

### Added

- Initial normalizator version.
