# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - YYYY-MM-DD

### Fixed

- ...

### Added

- Extension normalization: A predefined list of known extensions is now ignored.
- The `--not` option now supports also glob patterns.

## [0.0.6] - 2024-05-02

### Fixed

- Number of files in the log

### Added

- The `--not` option for skipping paths

## [0.0.5] - 2024-05-01

### Added

- Self update command version checking improvements.
- The `selfupdate` alias for the self update command.
- PHP 8.2 minimum required.
- Project dependencies updated.
- Permissions normalization checks shebang with more patterns.
- Patch files are now ignored in certain normalizations.

## [0.0.4] - 2023-07-03

### Fixed

- Code and test refactored and improved.

### Added

- Number of middle redundant lines added to report.
- File saving issues are added to logging report.

## [0.0.3] - 2023-06-26

### Fixed

- Command line options validation and resolving configuration.
- Finder iterator counting.
- Docker tag format fixed.

### Added

- Indentation style normalization.
- A list of paths can be entered to check and fix commands.
- Report contains number of redundant leading EOLs.
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
