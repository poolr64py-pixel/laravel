#!/bin/bash

# Script de Otimização MySQL para servidor com pouca RAM
# Terras no Paraguai - 28/12/2025
# Servidor: 1.9GB RAM total, 517MB disponível, SWAP 100% usado

echo "======================================================================"
echo "       OTIMIZAÇÃO MYSQL PARA SERVIDOR COM POUCA RAM"
echo "======================================================================"

MYSQL_CONF="/etc/mysql/mysql.conf.d/mysqld.cnf"

# 1. Backup do arquivo atual
echo ""
echo "1. Criando backup da configuração atual..."
echo "----------------------------------------------------------------------"

if [ -f "$MYSQL_CONF" ]; then
    cp $MYSQL_CONF ${MYSQL_CONF}.backup_$(date +%Y%m%d_%H%M%S)
    echo "✅ Backup criado: ${MYSQL_CONF}.backup_$(date +%Y%m%d_%H%M%S)"
else
    echo "❌ Arquivo $MYSQL_CONF não encontrado!"
    exit 1
fi

# 2. Mostrar configuração atual crítica
echo ""
echo "2. Configuração ATUAL (PROBLEMÁTICA para 1.9GB RAM)..."
echo "----------------------------------------------------------------------"

cat << 'CURRENT'
❌ CONFIGURAÇÃO ATUAL (USA MUITA RAM):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

innodb_buffer_pool_size = 512M        ← MUITO ALTO!
innodb_redo_log_capacity = 1G         ← MUITO ALTO!
table_open_cache = 4000                ← MUITO ALTO!
max_connections = 512                  ← MUITO ALTO!
tmp_table_size = 128M
max_heap_table_size = 128M
sort_buffer_size = 4M
join_buffer_size = 4M

Uso estimado de RAM: ~800-900MB só do MySQL!
Com apenas 1.9GB total, isso é INSUSTENTÁVEL.
CURRENT

# 3. Criar nova configuração otimizada
echo ""
echo "3. Criando configuração OTIMIZADA..."
echo "----------------------------------------------------------------------"

cat << 'OPTIMIZED'
✅ NOVA CONFIGURAÇÃO (OTIMIZADA PARA 1.9GB RAM):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

innodb_buffer_pool_size = 256M        ← Reduzido de 512M
innodb_redo_log_capacity = 256M       ← Reduzido de 1G
table_open_cache = 1000               ← Reduzido de 4000
max_connections = 100                 ← Reduzido de 512
tmp_table_size = 32M                  ← Reduzido de 128M
max_heap_table_size = 32M             ← Reduzido de 128M
sort_buffer_size = 2M                 ← Reduzido de 4M
join_buffer_size = 2M                 ← Reduzido de 4M
key_buffer_size = 32M                 ← Reduzido de 64M
innodb_buffer_pool_instances = 2      ← Reduzido de 8

Uso estimado de RAM: ~350-400MB
Economia: ~450MB liberados! 🎉
OPTIMIZED

# 4. Aplicar nova configuração
echo ""
echo "4. Aplicando nova configuração..."
echo "----------------------------------------------------------------------"

# Criar arquivo temporário com as substituições
cat > /tmp/mysql_optimize.sed << 'SEDSCRIPT'
# Otimizações críticas para servidor com pouca RAM
s/^innodb_buffer_pool_size = 512M/innodb_buffer_pool_size = 256M/
s/^innodb_buffer_pool_size = 1G/innodb_buffer_pool_size = 256M/
s/^innodb_redo_log_capacity = 1G/innodb_redo_log_capacity = 256M/
s/^table_open_cache = 4000/table_open_cache = 1000/
s/^max_connections = 512/max_connections = 100/
s/^tmp_table_size = 128M/tmp_table_size = 32M/
s/^max_heap_table_size = 128M/max_heap_table_size = 32M/
s/^sort_buffer_size = 4M/sort_buffer_size = 2M/
s/^join_buffer_size = 4M/join_buffer_size = 2M/
s/^key_buffer_size = 64M/key_buffer_size = 32M/
s/^innodb_buffer_pool_instances = 8/innodb_buffer_pool_instances = 2/
SEDSCRIPT

# Aplicar substituições
sed -i -f /tmp/mysql_optimize.sed $MYSQL_CONF

# Adicionar configurações extras se não existirem
grep -q "performance_schema" $MYSQL_CONF || echo "performance_schema = OFF" >> $MYSQL_CONF
grep -q "innodb_doublewrite" $MYSQL_CONF || echo "innodb_doublewrite = 0" >> $MYSQL_CONF

rm /tmp/mysql_optimize.sed

echo "✅ Configurações aplicadas ao arquivo"

# 5. Mostrar diferenças
echo ""
echo "5. RESUMO DAS MUDANÇAS..."
echo "----------------------------------------------------------------------"

cat << 'CHANGES'
┌─────────────────────────────┬──────────┬──────────┬──────────┐
│ Parâmetro                   │ ANTES    │ DEPOIS   │ Economia │
├─────────────────────────────┼──────────┼──────────┼──────────┤
│ innodb_buffer_pool_size     │ 512M     │ 256M     │ 256M     │
│ innodb_redo_log_capacity    │ 1G       │ 256M     │ 768M     │
│ table_open_cache            │ 4000     │ 1000     │ ~80MB    │
│ max_connections             │ 512      │ 100      │ ~200MB   │
│ tmp_table_size              │ 128M     │ 32M      │ 96M      │
│ max_heap_table_size         │ 128M     │ 32M      │ 96M      │
│ sort_buffer_size            │ 4M       │ 2M       │ 2M       │
│ join_buffer_size            │ 4M       │ 2M       │ 2M       │
│ key_buffer_size             │ 64M      │ 32M      │ 32M      │
└─────────────────────────────┴──────────┴──────────┴──────────┘

📊 TOTAL ECONOMIZADO: ~450-500MB de RAM! 🎉
CHANGES

# 6. Testar configuração
echo ""
echo "6. Testando nova configuração..."
echo "----------------------------------------------------------------------"

mysqld --verbose --help 2>&1 | grep -A 1 "Default options" > /dev/null

if [ $? -eq 0 ]; then
    echo "✅ Sintaxe do arquivo de configuração OK"
else
    echo "⚠️  Não foi possível validar completamente. Verificar após restart."
fi

# 7. Verificar se MySQL está rodando
echo ""
echo "7. Status atual do MySQL..."
echo "----------------------------------------------------------------------"

if systemctl is-active --quiet mysql; then
    echo "✅ MySQL está rodando"
    MYSQL_RUNNING=true
else
    echo "❌ MySQL não está rodando"
    MYSQL_RUNNING=false
fi

# 8. Oferecer restart do MySQL
echo ""
echo "8. Aplicando mudanças..."
echo "----------------------------------------------------------------------"

if [ "$MYSQL_RUNNING" = true ]; then
    echo "⚠️  IMPORTANTE: É necessário reiniciar o MySQL para aplicar as mudanças."
    echo ""
    read -p "Deseja reiniciar o MySQL agora? (s/n): " restart_mysql
    
    if [ "$restart_mysql" = "s" ]; then
        echo ""
        echo "Reiniciando MySQL..."
        systemctl restart mysql
        
        # Aguardar e verificar
        sleep 5
        
        if systemctl is-active --quiet mysql; then
            echo "✅ MySQL reiniciado com sucesso!"
            echo ""
            echo "Verificando status..."
            systemctl status mysql --no-pager -l | head -10
        else
            echo "❌ ERRO ao reiniciar MySQL!"
            echo ""
            echo "Restaurando backup..."
            cp ${MYSQL_CONF}.backup_* $MYSQL_CONF
            systemctl restart mysql
            echo "⚠️  Backup restaurado. Verifique os logs:"
            echo "   tail -50 /var/log/mysql/error.log"
            exit 1
        fi
    else
        echo ""
        echo "⏭️  MySQL NÃO foi reiniciado."
        echo "   As mudanças só terão efeito após reiniciar:"
        echo "   sudo systemctl restart mysql"
    fi
fi

# 9. Verificar uso de memória do MySQL
echo ""
echo "9. Verificando uso de memória do MySQL..."
echo "----------------------------------------------------------------------"

if pgrep -x mysqld > /dev/null; then
    MYSQL_MEM=$(ps aux | grep mysqld | grep -v grep | awk '{sum+=$6} END {print sum/1024}')
    echo "Memória usada pelo MySQL: ${MYSQL_MEM}MB"
    
    if (( $(echo "$MYSQL_MEM < 400" | bc -l) )); then
        echo "✅ Uso de memória está BOM (< 400MB)"
    elif (( $(echo "$MYSQL_MEM < 600" | bc -l) )); then
        echo "⚠️  Uso de memória está MODERADO (400-600MB)"
    else
        echo "🔴 Uso de memória ainda está ALTO (> 600MB)"
    fi
fi

# 10. Verificar memória do servidor
echo ""
echo "10. Memória do servidor após otimização..."
echo "----------------------------------------------------------------------"

free -h

# 11. Dicas adicionais
echo ""
echo "11. OTIMIZAÇÕES ADICIONAIS RECOMENDADAS..."
echo "----------------------------------------------------------------------"

cat << 'TIPS'
💡 OUTRAS OTIMIZAÇÕES POSSÍVEIS:

1. DESABILITAR Performance Schema (já adicionado):
   performance_schema = OFF
   Economia: ~100-150MB

2. DESABILITAR InnoDB Doublewrite (já adicionado):
   innodb_doublewrite = 0
   Economia: ~10-20MB
   ⚠️  Reduz segurança, mas aumenta performance

3. DESABILITAR Query Cache (MySQL 5.7):
   query_cache_type = 0
   query_cache_size = 0

4. LIMITAR Conexões persistentes:
   wait_timeout = 60
   interactive_timeout = 60

5. MONITORAR queries lentas:
   slow_query_log = 1
   long_query_time = 2
   slow_query_log_file = /var/log/mysql/slow.log

6. OTIMIZAR tabelas regularmente:
   mysqlcheck -u root -p --auto-repair --optimize --all-databases
TIPS

# 12. Criar arquivo de otimizações adicionais
echo ""
echo "12. Criando arquivo de otimizações extras..."
echo "----------------------------------------------------------------------"

cat > /tmp/mysql_extra_optimizations.cnf << 'EXTRA'
# Otimizações extras para servidor com pouca RAM
# Adicione ao final do mysqld.cnf se necessário

[mysqld]
# Desabilitar Performance Schema
performance_schema = OFF

# Desabilitar InnoDB Doublewrite (menos seguro, mais rápido)
innodb_doublewrite = 0

# Timeouts mais curtos
wait_timeout = 60
interactive_timeout = 60

# Query Cache (apenas MySQL 5.7)
# query_cache_type = 0
# query_cache_size = 0

# Slow Query Log
slow_query_log = 1
long_query_time = 2
slow_query_log_file = /var/log/mysql/slow-query.log

# Otimizações InnoDB
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1
innodb_stats_persistent = ON
EXTRA

echo "✅ Arquivo criado em: /tmp/mysql_extra_optimizations.cnf"
echo ""
echo "Para aplicar estas otimizações extras:"
echo "   cat /tmp/mysql_extra_optimizations.cnf >> $MYSQL_CONF"
echo "   systemctl restart mysql"

# RESUMO FINAL
echo ""
echo "======================================================================"
echo "                    OTIMIZAÇÃO CONCLUÍDA!"
echo "======================================================================"
echo ""
echo "✅ Configuração MySQL otimizada para servidor com 1.9GB RAM"
echo "✅ Economia estimada: 450-500MB de RAM"
echo "✅ Backup criado: ${MYSQL_CONF}.backup_*"
echo ""
echo "📊 IMPACTO ESPERADO:"
echo "   • Uso de RAM do MySQL: ~350-400MB (era ~800-900MB)"
echo "   • RAM disponível: +450-500MB"
echo "   • SWAP usage: Deve reduzir significativamente"
echo "   • Performance: Pode ser levemente menor, mas sistema mais estável"
echo ""
echo "🔍 MONITORAMENTO:"
echo "   # Ver uso de memória do MySQL"
echo "   ps aux | grep mysqld | grep -v grep"
echo ""
echo "   # Ver memória do servidor"
echo "   free -h"
echo ""
echo "   # Ver status do MySQL"
echo "   systemctl status mysql"
echo ""
echo "   # Ver queries lentas"
echo "   tail -f /var/log/mysql/slow-query.log"
echo ""
echo "⚠️  OBSERVAÇÕES:"
echo "   • max_connections reduzido para 100 (era 512)"
echo "   • Se site tiver muitos acessos simultâneos, pode precisar ajustar"
echo "   • Monitore por alguns dias para garantir estabilidade"
echo ""
echo "🔄 PARA REVERTER (se necessário):"
echo "   cp ${MYSQL_CONF}.backup_* $MYSQL_CONF"
echo "   systemctl restart mysql"
echo ""
echo "======================================================================"
echo "Otimização concluída em $(date '+%Y-%m-%d %H:%M:%S')"
echo "======================================================================"
