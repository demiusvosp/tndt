#! /bin/bash

export $(egrep -v '^#' .env | xargs)
echo "environment: ${APP_ENV}"

if [[ $1 == 'validate' ]]; then 
    docker exec -it tndt_php_1 ./bin/console doctrine:schema:validate
  
elif [[ $1 == 'update' ]]; then
    if [[ ${APP_ENV} == 'dev' ]]; then
        docker exec -it tndt_php_1 bin/console doctrine:schema:update --force

    else
        echo -e "\033[1;31m Run db_update safely in development. For other environments use migrations\033[0m"
        exit -1

    fi
elif [[ $1 == 'dump' ]]; then
    docker exec tndt_mysql_1 sh -c 'exec mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" "$DB_NAME"' > ./var/dumps/database.sql
else
    echo "Usage: ./db_schema.sh validate|update|dump"
  
fi


