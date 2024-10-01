#!/bin/bash

# Couleurs pour une interface shell plus propre
GREEN="\033[0;32m"
RED="\033[0;31m"
CYAN="\033[0;36m"
RESET="\033[0m"

echo -e "${CYAN}Stopping services and removing volumes...${RESET}"

# Exécution de docker-compose down
docker-compose down -v

# Vérification que docker-compose a correctement arrêté les conteneurs
if [ $? -eq 0 ]; then
    echo -e "${GREEN}Docker Compose stopped and volumes removed successfully.${RESET}"
else
    echo -e "${RED}Failed to stop Docker Compose or remove volumes.${RESET}"
    exit 1
fi

echo -e "${CYAN}All services stopped.${RESET}"
