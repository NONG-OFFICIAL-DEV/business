#!/bin/bash
set -e

ACTION=$1

DOCKER_USER=${DOCKER_USER:-$DOCKER_USERNAME}
TAG=${IMAGE_TAG:-latest}

BACKEND_IMAGE=$DOCKER_USER/inventory-backend
FRONTEND_IMAGE=$DOCKER_USER/inventory-frontend
WEBSERVER_IMAGE=$DOCKER_USER/inventory-webserver
AI_IMAGE=$DOCKER_USER/inventory-ai


echo "📦 Action: $ACTION"

build_images() {
  echo "🔨 Building images..."

  docker build -t $BACKEND_IMAGE:$TAG ./backend
  docker build -t $FRONTEND_IMAGE:$TAG ./frontend
  docker build -t $WEBSERVER_IMAGE:$TAG ./webserver
  docker build -t $AI_IMAGE:$TAG ./ai

  docker tag $BACKEND_IMAGE:$TAG $BACKEND_IMAGE:latest
  docker tag $FRONTEND_IMAGE:$TAG $FRONTEND_IMAGE:latest
  docker tag $WEBSERVER_IMAGE:$TAG $WEBSERVER_IMAGE:latest
  docker tag $AI_IMAGE:$TAG $AI_IMAGE:latest
  
}

push_images() {
  echo "📤 Pushing images..."

  docker push $BACKEND_IMAGE:latest
  docker push $FRONTEND_IMAGE:latest
  docker push $WEBSERVER_IMAGE:latest
  docker push $AI_IMAGE:latest
}

deploy_server() {
  echo "🚀 Deploying on server..."

  docker-compose down --remove-orphans
  docker-compose pull
  docker-compose up -d --remove-orphans
}

case "$ACTION" in
  build)
    build_images
    push_images
    ;;
  deploy)
    deploy_server
    ;;
  *)
    echo "❌ Unknown action: $ACTION"
    exit 1
    ;;
esac
