#!/bin/bash
docker compose down && docker volume rm  esu-advisor_mariadb -f && docker compose up --build
