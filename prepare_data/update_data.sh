#!/bin/bash
cd $(dirname $(readlink -e $0))
. ./config.sh

echo "#############################"
echo "#  Начало обработки данных  #"
echo "#############################"
. ./sh_scripts/routes_data.sh

echo "#############################"
echo "#       Очистка БД#         #"
echo "#############################"
psql -d $db_name -U $db_user -f "sql_scripts/pgsnapshot_schema_0.6.sql"

echo "#############################"
echo "#Начало загрузки данных в БД#"
echo "#############################"
. ./sh_scripts/data_to_db.sh

echo "#############################"
echo "#    Выборка данных в БД    #"
echo "#############################"
psql -d $db_name -U $db_user -f "sql_scripts/prepare_routes_data.sql"

echo "#############################"
echo "#  Скрипт завершил работу   #"
echo "#############################"