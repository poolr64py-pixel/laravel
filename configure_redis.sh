#!/bin/bash

# Script para configurar Redis no Laravel - Terras no Paraguai
# Data: 28/12/2025

PROJECT_DIR="/home/terrasnoparaguay/htdocs/www.terrasnoparaguay.com"

echo "======================================================================"
echo "       CONFIGURANDO REDIS NO LARAVEL"
echo "======================================================================"

cd $PROJECT_DIR

# 1. Backup do .env
echo ""
echo "1. Criando backup do .env..."
echo "----------------------------------------------------------------------"

cp .env .env.backup_redis_$(date +%Y%m%d_%H%M%S)
echo "✅ Backup criado: .env.backup_redis_$(date +%Y%m%d_%H%M%S)"

# 2. Verificar configurações atuais
echo ""
echo "2. Configurações atuais no .env..."
echo "----------------------------------------------------------------------"

echo "CACHE_DRIVER atual: $(grep "^CACHE_DRIVER=" .env | cut -d'=' -f2)"
echo "SESSION_DRIVER atual: $(grep "^SESSION_DRIVER=" .env | cut -d'=' -f2)"
echo "QUEUE_CONNECTION atual: $(grep "^QUEUE_CONNECTION=" .env | cut -d'=' -f2)"

# 3. Atualizar .env para usar Redis
echo ""
echo "3. Atualizando .env para usar Redis..."
echo "----------------------------------------------------------------------"

# Atualizar CACHE_DRIVER
if grep -q "^CACHE_DRIVER=" .env; then
    sed -i 's/^CACHE_DRIVER=.*/CACHE_DRIVER=redis/' .env
    echo "✅ CACHE_DRIVER atualizado para redis"
else
    echo "CACHE_DRIVER=redis" >> .env
    echo "✅ CACHE_DRIVER adicionado"
fi

# Atualizar SESSION_DRIVER
if grep -q "^SESSION_DRIVER=" .env; then
    sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=redis/' .env
    echo "✅ SESSION_DRIVER atualizado para redis"
else
    echo "SESSION_DRIVER=redis" >> .env
    echo "✅ SESSION_DRIVER adicionado"
fi

# Atualizar QUEUE_CONNECTION (opcional, mas recomendado)
if grep -q "^QUEUE_CONNECTION=" .env; then
    sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=redis/' .env
    echo "✅ QUEUE_CONNECTION atualizado para redis"
else
    echo "QUEUE_CONNECTION=redis" >> .env
    echo "✅ QUEUE_CONNECTION adicionado"
fi

# Verificar se existe configuração de Redis
if ! grep -q "^REDIS_HOST=" .env; then
    echo "" >> .env
    echo "# Redis Configuration" >> .env
    echo "REDIS_HOST=127.0.0.1" >> .env
    echo "REDIS_PASSWORD=null" >> .env
    echo "REDIS_PORT=6379" >> .env
    echo "✅ Configurações de conexão Redis adicionadas"
fi

# 4. Verificar novas configurações
echo ""
echo "4. Novas configurações no .env..."
echo "----------------------------------------------------------------------"

echo "CACHE_DRIVER: $(grep "^CACHE_DRIVER=" .env | cut -d'=' -f2)"
echo "SESSION_DRIVER: $(grep "^SESSION_DRIVER=" .env | cut -d'=' -f2)"
echo "QUEUE_CONNECTION: $(grep "^QUEUE_CONNECTION=" .env | cut -d'=' -f2)"
echo "REDIS_HOST: $(grep "^REDIS_HOST=" .env | cut -d'=' -f2)"
echo "REDIS_PORT: $(grep "^REDIS_PORT=" .env | cut -d'=' -f2)"

# 5. Limpar cache do Laravel
echo ""
echo "5. Limpando cache do Laravel..."
echo "----------------------------------------------------------------------"

php artisan config:clear
echo "✅ Config cache limpo"

php artisan cache:clear
echo "✅ Application cache limpo"

php artisan view:clear
echo "✅ View cache limpo"

php artisan route:clear
echo "✅ Route cache limpo"

# 6. Testar conexão Redis com Laravel
echo ""
echo "6. Testando conexão Redis com Laravel..."
echo "----------------------------------------------------------------------"

# Criar arquivo de teste temporário
cat > test_redis.php << 'PHPTEST'
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Testar cache
    Cache::put('test_key', 'Redis funcionando!', 60);
    $value = Cache::get('test_key');
    
    if ($value === 'Redis funcionando!') {
        echo "✅ Cache Redis funcionando!\n";
        echo "   Valor armazenado e recuperado com sucesso\n";
        Cache::forget('test_key');
    } else {
        echo "❌ Erro ao testar cache Redis\n";
        exit(1);
    }
    
    // Testar Redis diretamente
    $redis = Redis::connection();
    $redis->set('direct_test', 'Conexão direta OK');
    $direct_value = $redis->get('direct_test');
    
    if ($direct_value === 'Conexão direta OK') {
        echo "✅ Conexão direta Redis funcionando!\n";
        $redis->del('direct_test');
    }
    
    echo "\n🎉 Laravel está usando Redis corretamente!\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao conectar com Redis:\n";
    echo "   " . $e->getMessage() . "\n";
    exit(1);
}
PHPTEST

php test_redis.php
TESTE_RESULT=$?

rm test_redis.php

if [ $TESTE_RESULT -eq 0 ]; then
    echo ""
    echo "✅ Teste concluído com sucesso!"
else
    echo ""
    echo "⚠️  Houve erro no teste. Verificando..."
fi

# 7. Verificar estatísticas do Redis
echo ""
echo "7. Estatísticas do Redis após configuração..."
echo "----------------------------------------------------------------------"

redis-cli INFO stats | grep -E "total_connections_received|total_commands_processed|keyspace_hits|keyspace_misses"

# 8. Verificar chaves no Redis
echo ""
echo "8. Chaves atuais no Redis..."
echo "----------------------------------------------------------------------"

KEYS_COUNT=$(redis-cli DBSIZE | awk '{print $2}')
echo "Total de chaves no Redis: $KEYS_COUNT"

if [ "$KEYS_COUNT" -gt 0 ]; then
    echo ""
    echo "Primeiras chaves:"
    redis-cli KEYS "*" | head -10
fi

# 9. Otimizar configuração do Redis (opcional)
echo ""
echo "9. Recomendações de otimização Redis..."
echo "----------------------------------------------------------------------"

REDIS_MAXMEMORY=$(redis-cli CONFIG GET maxmemory | tail -1)

if [ "$REDIS_MAXMEMORY" = "0" ]; then
    echo "⚠️  Redis sem limite de memória configurado"
    echo ""
    echo "Recomendação: Configure um limite de memória"
    echo "Para configurar 256MB:"
    echo "   redis-cli CONFIG SET maxmemory 268435456"
    echo "   redis-cli CONFIG SET maxmemory-policy allkeys-lru"
    echo ""
    echo "Para tornar permanente, adicione no /etc/redis/redis.conf:"
    echo "   maxmemory 256mb"
    echo "   maxmemory-policy allkeys-lru"
else
    echo "✅ Redis com limite de memória: $REDIS_MAXMEMORY bytes"
fi

# RESUMO FINAL
echo ""
echo "======================================================================"
echo "                    CONFIGURAÇÃO COMPLETA!"
echo "======================================================================"
echo ""
echo "✅ Laravel agora está usando Redis para:"
echo "   • Cache de aplicação"
echo "   • Sessões de usuários"
echo "   • Filas de jobs (queue)"
echo ""
echo "📊 Benefícios:"
echo "   • Performance 5-10x mais rápida"
echo "   • Menor uso de disco"
echo "   • Cache compartilhado entre processos"
echo "   • Sessões mais confiáveis"
echo ""
echo "🔍 Monitoramento:"
echo "   • Ver estatísticas: redis-cli INFO stats"
echo "   • Ver memória usada: redis-cli INFO memory"
echo "   • Ver chaves: redis-cli KEYS '*laravel*'"
echo "   • Limpar cache: php artisan cache:clear"
echo ""
echo "📝 Arquivos modificados:"
echo "   • .env (backup criado)"
echo ""
echo "🔄 Para reverter (se necessário):"
echo "   cp .env.backup_redis_* .env"
echo "   php artisan config:clear"
echo ""
echo "======================================================================"
echo "Configuração concluída em $(date '+%Y-%m-%d %H:%M:%S')"
echo "======================================================================"
