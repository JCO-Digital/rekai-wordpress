.PHONY: install
install:
	@echo "Installing NPM and Composer dependencies"
	@pnpm install
	@pnpm composer:install

.PHONY: build
build: install
	@echo "Building plugin assets"
	@pnpm project:build

.PHONY: build-release
build-release: build
	@echo "Bundling a release of the project"
	@pnpm plugin:tasks
	@pnpm plugin:zip