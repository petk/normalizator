# Normalizator

![Test workflow](https://github.com/petk/normalizator/actions/workflows/tests.yaml/badge.svg)

Command line tool written in PHP that checks and fixes trailing whitespace, LF
or CRLF newline characters, redunant trailing final newlines, file permissions
and similar in given files.

## Features

* Checks file permissions
* Trims trailing whitespace
* Trims redundant final newlines
* Trims redundant leading newlines
* Inserts missing final newline
* Trims redundant newlines in the middle of the file
* Converts and syncs EOL (end of line) characters
* Cleans spaces before tabs in the indentation part of the line
* Checks path name if it contains any special characters (spaces, non-ascii
  characters...)
* Checks file extensions
* Checks file encodings

## Installation

Normalizator is a simple Phar executable file that can be downloaded from
GitHub:

Using curl:

```sh
curl -OL https://github.com/petk/normalizator/releases/latest/download/normalizator.phar
```

Using wget:

```sh
wget https://github.com/petk/normalizator/releases/latest/download/normalizator.phar
```

By moving it to `/usr/local/bin` it can be accessed accross your system:

```sh
chmod +x normalizator.phar
mv normalizator.phar /usr/local/bin/normalizator
```

## Update

To update normalizator to its latest version:

```sh
normalizator self-update
```

## About

There is a recurring and never-ending issue in many code projects with trailing
whitespace, missing final newlines at the end of the files, too many redundant
newlines, different end of line characters, misused permissions and similar.

Beside the multiple preferred coding styles, also certain editors behave
differently and some automatically fix these when saving files. Some utilize
`.editorconfig` file by default and some leave files intact.

Git also provides several
[configuration options](https://git-scm.com/docs/git-config) to detect and deal
with these issues.

Although it is not mandatory for all files to have these issues fixed, a more
consistent and homogeneous approach brings less cognitive load in commits and a
better development experience in certain text editors and IDEs.

This tool aims to bring an initial solution to this issue and provides a more
consistent set of source code files across the code repository with a simplistic
yet still useful and powerful enough approach to tidy all files in a given Git
repository, directory, or a path using command line.

## Docker

There is also a [Docker image](https://hub.docker.com/r/petk/normalizator)
available to run the tool inside a container:

```sh
docker run -it -v path/to/your/files/to/check:/opt/app:rw petk/normalizator:latest check .
```

## Usage

To check files (dry run) without modyfing any file:

```sh
normalizator check [OPTION...] [--] input-path
```

The `fix` command fixes and overwrites files:

```sh
normalizator fix [OPTION...] [--] input-path
```

### File encoding

For non-ASCII and non-UTF-8 file encodings, option `--encoding` or short `-c`
tries to convert file content encoding to UTF-8:

```sh
normalizator check --encoding -- ~/projects/path/to/files/
# or
normalizator check -c -- ~/projects/path/to/files/
```

See also:

* Article: [The Absolute Minimum Every Software Developer Absolutely, Positively Must Know About Unicode and Character Sets](https://www.joelonsoftware.com/2003/10/08/the-absolute-minimum-every-software-developer-absolutely-positively-must-know-about-unicode-and-character-sets-no-excuses/)

### Trailing whitespace

Normalizator can check and fix trailing whitespaces (spaces and tabs) by
trimming them in text files.

```sh
normalizator check --trailing-whitespace -- ~/projects/path/to/files/
# or
normalizator check -w -- ~/projects/path/to/files/
```

### Space before tab

To clean all spaces before tabs in the indentation:

```sh
normalizator check --space-before-tab -- ~/projects/path/to/files/
# or
normalizator check -s -- ~/projects/path/to/files/
```

### EOL normalization

This normalizes the EOL (end of line) style (LF vs CRLF):

```sh
normalizator check --eol -- ~/projects/path/to/files/
# or
normalizator check -e -- ~/projects/path/to/files/
```

According to [POSIX](https://pubs.opengroup.org/onlinepubs/9699919799.2018edition/basedefs/V1_chap03.html#tag_03_206),
a line is a sequence of zero or more non-`<newline>` characters plus a
terminating `<newline>` character. Files should normally have at least one
final newline character.

[C89 standard](https://port70.net/~nsz/c/c89/c89-draft.html#2.1.1.2) and
[above](https://port70.net/~nsz/c/c99/n1256.html#5.1.1.2) mention a final
newline:

> A source file that is not empty shall end in a new-line character, which shall
> not be immediately preceded by a backslash character.

Newline characters:

* LF (`\n`) (*nix and Mac, default)
* CRLF (`\r\n`) (Windows)
- CR (`\r`) (old Mac, obsolete)

### Permissions normalization

This syncs permissions of given files and directories. Symbolic links are not
affected.

```sh
normalizator check --permissions -- ~/projects/path/to/files/
# or
normalizator check -u -- ~/projects/path/to/files/
```

This mostly applies for *nix systems.

* Files should usually have 0644 permissions.
* Directories should usually have 0755 permissions.
* Executable files should usually have 0755 permissions.
* Protected files have 0444 permissions.

Permissions in the `.git` directory:

```
0755  └─ .git/
0755     ├─ branches/
0644     ├─ COMMIT_EDITMSG
0644     ├─ config
0755     ├─ hooks/
0755     │  ├─ applypatch-msg.sample
0755     │  ├─ commit-msg.sample
         │  └─ ...
0644     ├─ index
0755     ├─ info/
0755     ├─ logs/
0755     ├─ objects/
0755     │  ├─ 06/
0444     │  │  ├─ 1d542745facb8d2307059048b0c72746daf9a0
         │  │  └─ ...
0755     │  ├─ 08/
0755     │  ├─ 13/
         │  └─ ...
0644     ├─ packed-refs
0755     ├─ refs/
         └─ ...
```

On systems with umask 002 (Ubuntu) these permissions are off by 2. So they are a
bit more relaxed and defaults are:

* Files: 0664
* Directories 0775
* Executables 0755
* Protected files are still 0444

### Path normalization

To normalize filenames and directories:

```sh
normalizator check --path-name -- ~/projects/path/to/files/
# or
normalizator check -p -- ~/projects/path/to/files/
```

This fixes names of files and directories so that they don't contain whitespaces
or special characters that might cause issues in certain occassions. Also file
extension is checked in this step.

## Requirements

To use normalizator, system needs to have PHP 8.1 or greater installed with the
following core PHP extensions:

* intl
* mbstring

## Development

### Building `normalizator.phar`

After cloning the Git repository:

```sh
git clone https://github.com/petk/normalizator
cd normalizator
```

Install Composer dependencies:

```sh
composer install
```

To build a `normalizator.phar` file:

```sh
./bin/build
```

### Tests

Tests can be run in development with `phpunit`:

```sh
./vendor/bin/phpunit --display-warnings
```

PHPStan analysis can be executed in development:

```sh
./vendor/bin/phpstan analyse
```

### Building Docker image

To build Docker image run:

```sh
make build-docker
```

[Goss](https://github.com/goss-org/goss) is used for testing Docker image:

```sh
make test-docker
```

## License and contributing

Contributions are welcome by forking the repository on GitHub. This repository
is released under the MIT license.
