#!/bin/bash

# Скрипт для настройки домена в nginx.conf
# Использование: ./setup-domain.sh your-domain.com

if [ $# -eq 0 ]; then
    echo "Использование: $0 <domain>"
    echo "Пример: $0 exampreparation.ru"
    exit 1
fi

DOMAIN=$1

echo "Настройка домена: $DOMAIN"

# Замена домена в nginx.conf
sed -i "s/YOUR_DOMAIN/$DOMAIN/g" nginx.conf

echo "✅ Домен $DOMAIN настроен в nginx.conf"
echo "📝 Не забудьте получить SSL сертификат:"
echo "   sudo certbot certonly --standalone -d $DOMAIN -d www.$DOMAIN"
