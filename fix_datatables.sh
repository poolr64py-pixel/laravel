#!/bin/bash

FILE="public/assets/admin/js/custom.js"

# Substituir a configuração antiga pela nova
sed -i "s/  $('#basic-datatables').DataTable({/  $('#basic-datatables').DataTable({\n    pageLength: 50,/g" "$FILE"

# Mudar lengthChange de false para true para permitir o usuário escolher
sed -i "s/lengthChange: false,/lengthChange: true,/g" "$FILE"

echo "✅ DataTables configurado para mostrar 50 itens por página"
echo "✅ Usuário pode alterar entre 10, 25, 50, 100 itens"
