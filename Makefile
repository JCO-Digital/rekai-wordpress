.PHONY: install
install:
	@echo "Installing NPM and Composer dependencies"
	@pnpm install
	@pnpm composer:install

.PHONY: build
build: install
	@echo "Building plugin assets"
	@pnpm project:build