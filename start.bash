#!/bin/bash

# Couleurs pour une interface shell plus propre
GREEN="\033[0;32m"
RED="\033[0;31m"
CYAN="\033[0;36m"
RESET="\033[0m"

# Charger les variables depuis le fichier .env
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
else
    echo -e "${RED}.env file not found!${RESET}"
    exit 1
fi

# Mise à jour du fichier web/data/conf.json avec les valeurs de .env
CONF_FILE="web/data/config.json"

if [ -f $CONF_FILE ]; then
    echo -e "${CYAN}Updating conf.json with environment variables...${RESET}"

    # Modifier le contenu du fichier conf.json
    cat > $CONF_FILE <<EOL
{
    "hostname": "${DB_HOST}",
    "username": "${DB_USER}",
    "password": "${DB_PASSWORD}",
    "database": "travel_agency"
}
EOL

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}conf.json updated successfully.${RESET}"
    else
        echo -e "${RED}Failed to update conf.json.${RESET}"
        exit 1
    fi
else
    echo -e "${RED}conf.json file not found at $CONF_FILE!${RESET}"
    exit 1
fi

echo -e "${CYAN}Starting services with Docker Compose...${RESET}"

if docker-compose build php; then
    echo -e "${GREEN}Docker Compose built PHP.${RESET}"
else
    echo -e "${RED}Docker Compose failed to build PHP.${RESET}"
    exit 1
fi

# Exécution de docker-compose up
docker-compose up -d

# Vérification que docker-compose a démarré les conteneurs correctement
if [ $? -eq 0 ]; then
    echo -e "${GREEN}Docker Compose started successfully.${RESET}"
else
    echo -e "${RED}Failed to start Docker Compose.${RESET}"
    exit 1
fi

# Affichage des conteneurs actifs
echo -e "${CYAN}Checking running containers...${RESET}"
docker ps --format "table {{.Names}}\t{{.Status}}"

# Vérification que tous les conteneurs sont en cours d'exécution
RUNNING_CONTAINERS=$(docker ps -q | wc -l)
EXPECTED_CONTAINERS=$(docker-compose ps -q | wc -l)

if [ "$RUNNING_CONTAINERS" -eq "$EXPECTED_CONTAINERS" ]; then
    echo -e "${GREEN}All containers are up and running.${RESET}"
else
    echo -e "${RED}Warning: Not all containers are running. Check with 'docker-compose ps'.${RESET}"
fi

# Obtenir l'IP de WSL
echo -e "${CYAN}Retrieving WSL IP address...${RESET}"
WSL_IP=$(hostname -I | awk '{print $1}')

if [ -z "$WSL_IP" ]; then
    echo -e "${RED}Failed to retrieve WSL IP address.${RESET}"
else
    echo -e "${GREEN}WSL IP address: http://$WSL_IP${RESET}"
fi

echo -e "${CYAN}All tasks completed.${RESET}"
