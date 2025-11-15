#!/bin/bash

set -e  # Exit on any error

CONTAINER_NAME="pluginpass-wordpress"
PLUGINS_EXTERNAL_PATH="/plugins-external"
PLUGINS_TARGET_PATH="/var/www/html/wp-content/plugins"

# Site install defaults
WP_URL="http://localhost:8000"
WP_TITLE="PluginPass Dev Site"
WP_ADMIN_USER="pluginpass"
WP_ADMIN_PASSWORD="pluginpass"
WP_ADMIN_EMAIL="pluginpass@local.local"

echo -e "\nStopping containers and removing volumes..."
docker compose down --volumes

echo -e "\nBuilding WordPress image without cache..."
docker compose build wordpress --no-cache

echo -e "\nStarting containers in detached mode..."
docker compose up -d

echo -e "\nWaiting for WordPress container to be ready..."
for i in {1..30}; do
  if docker inspect -f '{{.State.Health.Status}}' $CONTAINER_NAME | grep -q "healthy"; then
    echo "WordPress container is healthy."
    break
  fi
  sleep 2
done

if ! docker inspect -f '{{.State.Health.Status}}' $CONTAINER_NAME | grep -q "healthy"; then
  echo "Error: WordPress container did not become healthy in time."
  exit 1
fi

echo -e "\nRunning Composer update inside the container..."
docker exec $CONTAINER_NAME bash -c "
  cd $PLUGINS_EXTERNAL_PATH/pluginpass-pro-plugintheme-licensing &&
  rm -rf vendor &&
  composer update --prefer-dist --no-interaction --no-dev --optimize-autoloader --ignore-platform-reqs ||
  echo 'Composer update failed'
"

echo -e "\nInstalling WordPress site if not present..."
docker exec $CONTAINER_NAME bash -c "
  if ! wp core is-installed --allow-root; then
    wp core install \
      --url='$WP_URL' \
      --title='$WP_TITLE' \
      --admin_user='$WP_ADMIN_USER' \
      --admin_password='$WP_ADMIN_PASSWORD' \
      --admin_email='$WP_ADMIN_EMAIL' \
      --allow-root \
      --skip-email ||
      echo 'Failed to install WordPress site'
  else
    echo 'WordPress site already installed'
  fi
"


echo -e "\nAutoinstall Plugin Check (PCP) if not present..."
docker exec $CONTAINER_NAME bash -c "
  if ! wp plugin is-installed plugin-check --allow-root; then
    wp plugin install plugin-check --activate --allow-root ||
      echo 'Failed to install Plugin Check (PCP)'
  else
    echo 'Plugin Check (PCP) already installed'
  fi;
"

echo -e "\nCreating symlinks for plugins inside the container..."
docker exec $CONTAINER_NAME bash -c "
  if [ ! -L $PLUGINS_TARGET_PATH/pluginpass-pro-plugintheme-licensing ]; then
    ln -sf $PLUGINS_EXTERNAL_PATH/pluginpass-pro-plugintheme-licensing $PLUGINS_TARGET_PATH/pluginpass-pro-plugintheme-licensing ||
      echo 'Failed to create symlink for pluginpass-pro-plugintheme-licensing'
  else
    echo 'Symlink for pluginpass-pro-plugintheme-licensing already exists'
  fi

  if [ ! -L $PLUGINS_TARGET_PATH/pluginpass-demo ]; then
    ln -sf $PLUGINS_EXTERNAL_PATH/pluginpass-demo $PLUGINS_TARGET_PATH/pluginpass-demo ||
      echo 'Failed to create symlink for pluginpass-demo'
  else
    echo 'Symlink for pluginpass-demo already exists'
  fi

  wp plugin list --allow-root
"

echo -e "\nScript completed."
