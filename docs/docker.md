# Normalizator Docker image

![Tests](https://github.com/petk/normalizator/actions/workflows/test-docker.yaml/badge.svg)

Docker image with [Normalizator](https://github.com/petk/normalizator) - command
line tool that checks and fixes trailing whitespace, end of lines LF or CRLF
characters, redundant trailing final newlines, file permissions and similar.

## Usage

```sh
docker run -it -v path/to/files:/opt/app:rw petk/normalizator:latest check .
```

## Docker tags

* [`latest` (*Dockerfile*)](https://github.com/petk/normalizator/tree/main/Dockerfile) - Alpine, PHP 8.5, Normalizator

## License and contributing

Contributions are welcome by forking the repository on GitHub. This repository
is released under the MIT license.
