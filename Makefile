.DEFAULT_GOAL := help
.PHONY: *
TAG ?= "latest"

help:
	@echo "\033[33mUsage:\033[0m\n  make [target] [arg=\"val\"...]\n\n\033[33mTargets:\033[0m"
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-15s\033[0m %s\n", $$1, $$2}'

test-docker: ## Run all Docker tests; Usage: make test-docker [t="<test-folder-1> <test-folder-2> ..."]
	@cd tests/docker; \
	./test "$(t)"

build-docker:
	@docker build -t petk/normalizator:$(TAG) -f Dockerfile .

push-docker: ## Push Docker image to Docker Hub; Usage: make push TAG=x.y.z
	@docker push petk/normalizator:$(TAG)

push-docker-latest: ## Create latest Docker tag and push it to Docker Hub
	@docker tag petk/normalizator:$(TAG) petk/normalizator:latest
	@docker push petk/normalizator:latest
