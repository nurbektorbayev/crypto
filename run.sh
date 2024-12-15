#!/bin/bash

# Функция для поднятия контейнеров
up() {
  SERVICE=$1
  ENVIRONMENT=$2

  # Если окружение не указано, используем dev по умолчанию
  ENVIRONMENT=${ENVIRONMENT:-dev}

  # Путь до docker-compose для указанного сервиса и окружения
  DOCKER_COMPOSE_PATH="./services/$SERVICE/docker/docker-compose.$ENVIRONMENT.yml"

  # Проверяем, существует ли файл docker-compose для указанного сервиса и окружения
  if [ ! -f "$DOCKER_COMPOSE_PATH" ]; then
    echo "Файл docker-compose для сервиса '$SERVICE' с окружением '$ENVIRONMENT' не найден."
    exit 1
  fi

  # Проверка и создание .env, если он не существует
  ENV_FILE="./services/$SERVICE/docker/.env"
  ENV_EXAMPLE="./services/$SERVICE/docker/.env.example"

  if [ ! -f "$ENV_FILE" ] && [ -f "$ENV_EXAMPLE" ]; then
    echo ".env файл не найден. Создаем его на основе .env.example..."
    cp "$ENV_EXAMPLE" "$ENV_FILE"
    echo ".env файл успешно создан."
  elif [ ! -f "$ENV_EXAMPLE" ]; then
    echo ".env.example не найден. .env файл не будет создан."
  fi

  # Запускаем docker-compose для указанного сервиса и окружения в фоновом режиме
  echo "Запуск сервиса $SERVICE с окружением $ENVIRONMENT в фоновом режиме..."
  docker compose -f $DOCKER_COMPOSE_PATH up -d --build
}

# Функция для остановки контейнеров
down() {
  SERVICE=$1
  ENVIRONMENT=$2

  # Если окружение не указано, используем dev по умолчанию
  ENVIRONMENT=${ENVIRONMENT:-dev}

  # Путь до docker-compose для указанного сервиса и окружения
  DOCKER_COMPOSE_PATH="./services/$SERVICE/docker/docker-compose.$ENVIRONMENT.yml"

  # Проверяем, существует ли файл docker-compose для указанного сервиса и окружения
  if [ ! -f "$DOCKER_COMPOSE_PATH" ]; then
    echo "Файл docker-compose для сервиса '$SERVICE' с окружением '$ENVIRONMENT' не найден."
    exit 1
  fi

  # Останавливаем контейнеры с помощью docker-compose
  echo "Остановка сервиса $SERVICE с окружением $ENVIRONMENT..."
  docker compose -f $DOCKER_COMPOSE_PATH down
}

# Функция для входа в контейнер
exec_in_container() {
  SERVICE=$1
  CONTAINER_NAME_SUFFIX=$2

  # Если суффикс не указан, используем app по умолчанию
  CONTAINER_NAME_SUFFIX=${CONTAINER_NAME_SUFFIX:-app}

  # Собираем имя контейнера
  CONTAINER_NAME="${SERVICE}_${CONTAINER_NAME_SUFFIX}"

  # Проверяем, доступен ли bash в контейнере
  if docker exec $CONTAINER_NAME which bash &>/dev/null; then
    SHELL="bash"
  else
    SHELL="sh"
  fi

  # Входим в контейнер с найденным shell
  echo "Вход в контейнер $CONTAINER_NAME для сервиса $SERVICE с использованием $SHELL..."
  docker exec -it $CONTAINER_NAME $SHELL
}

# Преобразуем alias для api_gateway в api
SERVICE=$2
if [ "$SERVICE" == "api" ]; then
  SERVICE="api_gateway"
fi

# Проверяем, какой аргумент был передан
if [ -z "$1" ]; then
  echo "Пожалуйста, укажите команду (up, down или exec) и сервис для запуска. Пример: ./run.sh up users"
  exit 1
fi

COMMAND=$1
ENVIRONMENT=$3  # Третья переменная - это окружение, например, dev или prod. Является суффиксом контейнера при exec

# В зависимости от команды выполняем нужную функцию
if [ "$COMMAND" == "up" ]; then
  up $SERVICE $ENVIRONMENT
elif [ "$COMMAND" == "down" ]; then
  down $SERVICE $ENVIRONMENT
elif [ "$COMMAND" == "exec" ]; then
  exec_in_container $SERVICE $ENVIRONMENT
else
  echo "Неизвестная команда '$COMMAND'. Используйте 'up', 'down' или 'exec'."
  exit 1
fi
