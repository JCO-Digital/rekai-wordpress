.PHONY: install
install:
	@echo "📦 Installing NPM and Composer dependencies"
	@pnpm install
	@pnpm composer:install

.PHONY: install-release
install-release:
	@echo "📦❗ Installing release NPM and Composer dependencies"
	@pnpm install
	@pnpm composer:install-release

.PHONY: build
build: install
	@echo "🛠️ Building plugin assets"
	@pnpm project:build

.PHONY: build-release
build-release: install-release
	@echo "🛠️❗ Building a release of the project"
	@pnpm project:build

.PHONY: build-release
release: build-release
	@echo "✅❗ Bundling a release of the project"
	@pnpm plugin:tasks
	@pnpm plugin:dist
	@pnpm plugin:zip

.PHONY: clean-blocks
clean-blocks:
	@echo "Cleaning blocks"
	@rm -rf blocks/build

.PHONY: dev
dev: clean-blocks install
	@echo "▶️ Starting project"
	@pnpm project:dev

.PHONY: test
test:
	@echo "▶️ Running tests"
	@pnpm composer:test
