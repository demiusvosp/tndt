#! /bin/bash

if [[ $1 == 'validate' ]]; then 
  docker exec -it yatt_php_1 ./bin/console doctrine:schema:validate
  
elif [[ $1 == 'update' ]]; then
  docker exec -it yatt_php_1 bin/console doctrine:schema:update --force
  
else
  echo "Usage: ./db_schema.sh validate|update"
  
fi


