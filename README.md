# Project: API Gateway

## Description

API Gateway is a microservice application implemented using Laravel and Nginx. The project is designed for routing and managing API requests. The application's infrastructure is containerized using Docker.

---

## Table of Contents

1. [Requirements](#requirements)
2. [Installation and Setup](#installation-and-setup)
    - [Step 1: Clone the Repository](#step-1-clone-the-repository)
    - [Step 2: Configure the Environment](#step-2-configure-the-environment)
    - [Step 3: Build and Start Containers](#step-3-build-and-start-containers)
3. [Project Structure](#project-structure)
4. [Management Script](#management-script)
5. [Useful Commands](#useful-commands)
6. [License](#license)

---

## Requirements

- Docker and Docker Compose version 2.0 or higher
- Git for cloning the repository

---

## Installation and Setup

### Step 1: Clone the Repository

Clone the project repository to your local machine:

```bash
git clone <repository URL>
cd <project directory>
```

### Step 2: Configure the Environment

- Navigate to the directory of the required microservice, for example, `services/api_gateway/docker`.
- Ensure the `.env` file exists. If it doesn't, it will be automatically created based on `.env.example` during the startup process.

### Step 3: Build and Start Containers

Start the containers using the provided script:

```bash
./run.sh up api_gateway
```

To start in a different environment (e.g., production):

```bash
./run.sh up api_gateway prod
```

---

## Project Structure

```
├── docker/
│   ├── app/
│   │   ├── Dockerfile
│   │   ├── supervisord.conf
│   ├── db/
│   │   ├── data/
│   └── run.sh
├── services/
│   ├── api_gateway/
│   │   ├── docker/
│   │   │   ├── docker-compose.dev.yml
│   │   │   ├── docker-compose.prod.yml
│   │   │   ├── crypto.local.conf
│   │   │   ├── .env.example
│   │   ├── source/
│   │   │   └── Laravel project
├── .gitignore
└── README.md
```

---

## Management Script

The `run.sh` script is used for managing containers:

- **Start containers**:
  ```bash
  ./run.sh up <SERVICE_NAME> [ENVIRONMENT]
  ```
- **Stop containers**:
  ```bash
  ./run.sh down <SERVICE_NAME> [ENVIRONMENT]
  ```
- **Access a container**:
  ```bash
  ./run.sh exec <SERVICE_NAME> [CONTAINER_SUFFIX]
  ```
  Examples:
  ```bash
  ./run.sh exec api_gateway
  ./run.sh exec api_gateway nginx
  ```

---

## Useful Commands

### Check Containers

```bash
docker compose -f ./services/<SERVICE_NAME>/docker/docker-compose.<ENVIRONMENT>.yml ps
```

### Check Logs

```bash
docker logs <CONTAINER_NAME>
```

### Check PHP-FPM Availability

Inside the container:

```bash
curl http://localhost:9000
```

---

## License

This project is licensed under the MIT License. See the LICENSE file for details.

