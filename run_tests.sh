#!/bin/bash

# Instala dependências se necessário
if [ ! -d "vendor" ]; then
    composer install
fi

# Executa os testes
./vendor/bin/phpunit --testdox

# Se quiser gerar relatório de cobertura, descomente a linha abaixo
# ./vendor/bin/phpunit --coverage-html coverage 