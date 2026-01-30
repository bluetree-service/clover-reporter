.PHONY: up \
		build \
		clean \
		init \
		logo \
		push

ESC := \033
RED := $(ESC)[0;31m
GREEN := $(ESC)[0;32m
YELLOW := $(ESC)[1;33m
BLUE := $(ESC)[0;34m
PURPLE := $(ESC)[0;35m
CYAN := $(ESC)[0;36m
NC := $(ESC)[0m

ifneq ($(NO_COLOR),)
RED :=
GREEN :=
YELLOW :=
BLUE :=
PURPLE :=
CYAN :=
NC :=
endif

PRINT = printf '%b\n' "$(1)"

DOCKER=docker
DOCKER_COMPOSE=$(DOCKER) compose
LOCAL_USER_ID=$(shell id -u)
tag?=latest
platform?=linux/amd64
#platform?=linux/arm64
php_version?=82
PHP_VERSION:=$(php_version)
RUN_COMMAND?=php-fpm
daemon?=0
container_mode?=0
compose_file?=tests/docker-compose
DOCKER_COMPOSE_COMMAND=$(DOCKER_COMPOSE) -f $(compose_file)$(PHP_VERSION).yaml
MAKE=make
mount_local=0

DAEMON=
ifeq (1,$(daemon))
	DAEMON=-d
endif

CONTAINER_MODE=
CONNTAINER_MODE_MESSAGE=
ifeq (1,$(container_mode))
	CONTAINER_MODE=$(DOCKER_COMPOSE_COMMAND) exec -u www-data php$(PHP_VERSION)
	CONNTAINER_MODE_MESSAGE=$(BLUE)ℹ️ Running command inside php container...$(NC)
endif

MOUNT_POINT=../:/var/www/origin
ifeq (1,$(mount_local))
	MOUNT_POINT=../:/var/www/html
endif

export DOCKER_TAG:=$(tag)
export tag
export LOCAL_USER_ID
export PHP_VERSION
export RUN_COMMAND
export MOUNT_POINT

init:
	@$(call PRINT,$(CYAN)🏷️ Docker Tag: $(NC)$(DOCKER_TAG))
	@$(call PRINT,$(CONNTAINER_MODE_MESSAGE))

build: init
	@$(call PRINT,$(BLUE)🔨 Building...$(NC))
	@$(CONTAINER_MODE) composer install

up: init
	@$(call PRINT,$(BLUE)🚀 Starting environment...$(NC))
	$(DOCKER_COMPOSE_COMMAND) up $(DAEMON)

down:
	@echo "$(BLUE)🛑 Stopping and removing environment...$(NC)"
	$(DOCKER_COMPOSE_COMMAND) down --remove-orphans

stop: init
	@echo "$(BLUE)🛑 Stopping environment...$(NC)"
	$(DOCKER_COMPOSE_COMMAND) stop

test: init
	@$(call PRINT,$(BLUE)🧪 Running tests...$(NC))
	$(CONTAINER_MODE) composer test

composer-install: init
	@echo "$(BLUE)💻 Connect with container...$(NC)"
	$(CONTAINER_MODE) composer install

test-all: init
	@echo "$(BLUE)🧪 Running all tests...$(NC)"
	@for file in tests/*.yaml; do \
		echo "===> Testing with $$file"; \
		version=$$(echo "$$file" | sed -E 's/.*([0-9]{2})\.yaml/\1/'); \
		echo "🛠 PHP version:$(BLUE) $$version $(NC)"; \
		$(MAKE) run-and-test php_version=$${version}; \
		echo "====================================================================================================="; \
	done

run-and-test: init
	@echo "$(BLUE)🚀 Running environment and tests...$(NC)"
	$(MAKE) up daemon=1 php_version=$(PHP_VERSION)
	$(MAKE) copy php_version=$(PHP_VERSION)
	$(MAKE) build container_mode=1 php_version=$(PHP_VERSION)
	$(MAKE) test container_mode=1 php_version=$(PHP_VERSION)
	$(MAKE) down php_version=$(PHP_VERSION)

copy: init
	@echo "$(BLUE)💻 Copy files to container...$(NC)"
	@$(DOCKER_COMPOSE_COMMAND) cp . php$(PHP_VERSION):/var/www/html
	@$(DOCKER_COMPOSE_COMMAND) exec php$(PHP_VERSION) chown -R www-data:www-data /var/www/html

run: up copy build

coverage:
	@$(call PRINT,$(BLUE)🧪 Running tests with coverage...$(NC))
	$(CONTAINER_MODE) composer coverage

coverage-status: coverage
	@$(call PRINT,$(BLUE)🧪 Coverage status...$(NC))
	$(CONTAINER_MODE) composer clover-report

coverage-status-full: coverage
	@$(call PRINT,$(BLUE)🧪 Coverage status...$(NC))
	$(CONTAINER_MODE) composer clover-report-full

exec: init
	@$(call PRINT,$(BLUE)💻 Connect with container...$(NC))
	$(CONTAINER_MODE) bash
