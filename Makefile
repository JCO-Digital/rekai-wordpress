.PHONY: install
install:
	@echo "Installing NPM and Composer dependencies"
	@pnpm install
	@pnpm composer:install

install-release:
	@echo "Installing NPM and Composer dependencies"
	@pnpm install
	@pnpm composer:install-release

.PHONY: build
build: install
	@echo "Building plugin assets"
	@pnpm project:build

.PHONY: build-release
build-release: install-release
	@echo "Building a release of the project"
	@pnpm project:build

.PHONY: build-release
release: build-release
	@echo "Bundling a release of the project"
	@pnpm plugin:tasks
	@pnpm plugin:zip

.PHONY: dev
dev: install
	@echo "Starting project"
	@pnpm project:dev