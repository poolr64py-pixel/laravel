#!/bin/bash
# Script de manutenção automática

cd /home/terrasnoparaguay/htdocs/www.terrasnoparaguay.com

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Limpar logs antigos (mais de 30 dias)
find storage/logs/ -name "*.log" -mtime +30 -delete

# Otimizar tabelas do banco
mysql -u terrasnoparaguay -p'25Paulor+*&%$' multiestate2025 << 'EOF'
OPTIMIZE TABLE user_properties;
OPTIMIZE TABLE user_projects;
EOF

echo "Manutenção concluída em $(date)"
