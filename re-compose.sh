#!/bin/bash
docker compose down && docker image rm esu-advisor-web:latest && docker volume rm  esu-advisor_mariadb && docker buildx prune -f && docker compose up
