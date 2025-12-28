#!/bin/bash

# Script para configurar limite de memória Redis
# Terras no Paraguai - 28/12/2025

echo "======================================================================"
echo "       CONFIGURANDO LIMITE DE MEMÓRIA DO REDIS"
echo "======================================================================"

# 1. Verificar memória disponível no servidor
echo ""
echo "1. Verificando memória disponível no servidor..."
echo "----------------------------------------------------------------------"

free -h

TOTAL_MEM=$(free -m | awk 'NR==2 {print $2}')
AVAILABLE_MEM=$(free -m | awk 'NR==2 {print $7}')

echo ""
echo "Memória total: ${TOTAL_MEM}MB"
echo "Memória disponível: ${AVAILABLE_MEM}MB"

# 2. Recomendar tamanho baseado na memória disponível
echo ""
echo "2. Calculando limite recomendado..."
echo "----------------------------------------------------------------------"

if [ "$TOTAL_MEM" -ge 8000 ]; then
    RECOMMENDED_MB=512
    RECOMMENDED_BYTES=536870912
    echo "Servidor com muita memória (${TOTAL_MEM}MB)"
    echo "Recomendação: 512MB para Redis"
elif [ "$TOTAL_MEM" -ge 4000 ]; then
    RECOMMENDED_MB=256
    RECOMMENDED_BYTES=268435456
    echo "Servidor com boa memória (${TOTAL_MEM}MB)"
    echo "Recomendação: 256MB para Redis"
elif [ "$TOTAL_MEM" -ge 2000 ]; then
    RECOMMENDED_MB=128
    RECOMMENDED_BYTES=134217728
    echo "Servidor com memória moderada (${TOTAL_MEM}MB)"
    echo "Recomendação: 128MB para Redis"
else
    RECOMMENDED_MB=64
    RECOMMENDED_BYTES=67108864
    echo "Servidor com pouca memória (${TOTAL_MEM}MB)"
    echo "Recomendação: 64MB para Redis"
fi

echo ""
echo "✅ Limite recomendado: ${RECOMMENDED_MB}MB"

# 3. Aplicar configuração temporária (ativa imediatamente)
echo ""
echo "3. Aplicando configuração temporária (ativa agora)..."
echo "----------------------------------------------------------------------"

redis-cli CONFIG SET maxmemory $RECOMMENDED_BYTES
redis-cli CONFIG SET maxmemory-policy allkeys-lru

echo "✅ Limite de memória aplicado: ${RECOMMENDED_MB}MB"
echo "✅ Política de remoção: allkeys-lru (remove chaves menos usadas)"

# 4. Verificar configuração aplicada
echo ""
echo "4. Verificando configuração atual..."
echo "----------------------------------------------------------------------"

CURRENT_MAXMEM=$(redis-cli CONFIG GET maxmemory | tail -1)
CURRENT_POLICY=$(redis-cli CONFIG GET maxmemory-policy | tail -1)

echo "Limite de memória: $CURRENT_MAXMEM bytes ($(($CURRENT_MAXMEM / 1024 / 1024))MB)"
echo "Política de remoção: $CURRENT_POLICY"

# 5. Tornar configuração permanente
echo ""
echo "5. Tornando configuração permanente..."
echo "----------------------------------------------------------------------"

REDIS_CONF="/etc/redis/redis.conf"

if [ -f "$REDIS_CONF" ]; then
    # Backup do arquivo de configuração
    cp $REDIS_CONF ${REDIS_CONF}.backup_$(date +%Y%m%d_%H%M%S)
    echo "✅ Backup criado: ${REDIS_CONF}.backup_$(date +%Y%m%d_%H%M%S)"
    
    # Remover configurações antigas se existirem
    sed -i '/^maxmemory /d' $REDIS_CONF
    sed -i '/^maxmemory-policy /d' $REDIS_CONF
    
    # Adicionar novas configurações
    cat >> $REDIS_CONF << EOF

# Configuração de limite de memória - Adicionado em $(date)
maxmemory ${RECOMMENDED_MB}mb
maxmemory-policy allkeys-lru

# Outras otimizações
save 900 1
save 300 10
save 60 10000
tcp-keepalive 300
timeout 0
EOF
    
    echo "✅ Configurações adicionadas ao $REDIS_CONF"
    
    # Testar configuração
    echo ""
    echo "Testando configuração do Redis..."
    redis-server $REDIS_CONF --test-memory $RECOMMENDED_MB 2>&1 | head -5
    
else
    echo "⚠️  Arquivo $REDIS_CONF não encontrado"
    echo ""
    echo "Configurações aplicadas temporariamente, mas não serão mantidas após reiniciar."
    echo ""
    echo "Para aplicar manualmente, adicione ao arquivo de configuração do Redis:"
    echo "   maxmemory ${RECOMMENDED_MB}mb"
    echo "   maxmemory-policy allkeys-lru"
fi

# 6. Verificar uso atual de memória
echo ""
echo "6. Uso atual de memória Redis..."
echo "----------------------------------------------------------------------"

redis-cli INFO memory | grep -E "used_memory_human|used_memory_peak_human|used_memory_rss_human|maxmemory_human"

# 7. Informações sobre políticas de remoção
echo ""
echo "7. Explicação das políticas de remoção..."
echo "----------------------------------------------------------------------"

cat << 'POLICIES'
Política configurada: allkeys-lru
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ allkeys-lru (RECOMENDADO para Laravel)
   Remove as chaves menos recentemente usadas (LRU) de todas as chaves.
   Ideal para cache geral de aplicação.

Outras opções disponíveis:
   • volatile-lru: Remove LRU apenas de chaves com TTL
   • allkeys-lfu: Remove as chaves menos frequentemente usadas
   • volatile-lfu: Remove LFU apenas de chaves com TTL
   • allkeys-random: Remove chaves aleatórias
   • volatile-random: Remove chaves aleatórias com TTL
   • volatile-ttl: Remove chaves com menor TTL
   • noeviction: Nunca remove (retorna erro quando cheio)

Para Laravel com cache e sessões: allkeys-lru é a melhor escolha! ✅
POLICIES

# 8. Comandos úteis de monitoramento
echo ""
echo "8. Comandos úteis para monitoramento..."
echo "----------------------------------------------------------------------"

cat << 'COMMANDS'
# Ver uso de memória em tempo real
redis-cli INFO memory | grep used_memory_human

# Monitorar comandos em tempo real
redis-cli MONITOR

# Ver estatísticas
redis-cli INFO stats

# Ver todas as chaves
redis-cli KEYS "*"

# Limpar todo o cache Redis
redis-cli FLUSHALL

# Ver chaves do Laravel
redis-cli KEYS "laravel*"

# Limpar apenas cache do Laravel
php artisan cache:clear
COMMANDS

# RESUMO FINAL
echo ""
echo "======================================================================"
echo "                    CONFIGURAÇÃO CONCLUÍDA!"
echo "======================================================================"
echo ""
echo "✅ Limite de memória configurado: ${RECOMMENDED_MB}MB"
echo "✅ Política de remoção: allkeys-lru"
echo "✅ Configuração aplicada e permanente"
echo ""
echo "📊 Status atual:"

redis-cli INFO memory | grep -E "used_memory_human|maxmemory_human" | sed 's/^/   /'

echo ""
echo "🔍 Monitoramento:"
echo "   • Verificar uso: redis-cli INFO memory | grep used"
echo "   • Ver logs: tail -f /var/log/redis/redis-server.log"
echo "   • Estatísticas: redis-cli INFO stats"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   Redis agora tem limite de ${RECOMMENDED_MB}MB."
echo "   Quando atingir o limite, removerá automaticamente"
echo "   as chaves menos usadas (allkeys-lru)."
echo ""
echo "🔄 Para ajustar o limite no futuro:"
echo "   redis-cli CONFIG SET maxmemory <bytes>"
echo "   Editar: $REDIS_CONF"
echo ""
echo "======================================================================"
echo "Configuração concluída em $(date '+%Y-%m-%d %H:%M:%S')"
echo "======================================================================"
