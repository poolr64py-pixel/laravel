# ⚠️ TODO: Menu Mobile - Refatoração Futura

## Status Atual
✅ **Funcionando** mas com solução temporária

## O Que Foi Feito
- Arquivo: `public/assets/front/js/menu-mobile-simple.js`
- Método: `onclick` direto (primitivo mas funcional)
- Dropdowns: Imóveis + Aluguéis

## Por Que É Temporário
- Usa `onclick` ao invés de `addEventListener` 
- Não está integrado ao `script.js` principal do tema
- Sem transições suaves
- Código isolado (não segue padrão do tema)

## Refatoração Recomendada (Futuro)
1. Estudar o código original do tema em `script.js`
2. Integrar menu mobile seguindo o mesmo padrão
3. Adicionar transições CSS suaves
4. Remover `menu-mobile-simple.js` separado
5. Testar em todos os dispositivos

**Estimativa:** 2-3 horas de trabalho

**Prioridade:** Baixa (funciona, não quebra)

---
**Criado em:** 02/02/2026
**Motivo:** Falta de tempo no momento da implementação
