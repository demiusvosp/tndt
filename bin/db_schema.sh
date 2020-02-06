#! /bin/bash

export $(egrep -v '^#' .env | xargs)
echo "environment: ${APP_ENV}"

if [[ $1 == 'validate' ]]; then 
    docker exec -it yatt_php_1 ./bin/console doctrine:schema:validate
  
elif [[ $1 == 'update' ]]; then
    if [[ ${APP_ENV} == 'dev' ]]; then
        docker exec -it yatt_php_1 bin/console doctrine:schema:update --force

    else
        echo -e "\033[1;31m Run db_update safely in development. For other environments use migrations\033[0m"
        exit -1

    fi
  
else
    echo "Usage: ./db_schema.sh validate|update"
  
fi


