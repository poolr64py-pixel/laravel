#!/bin/bash

# Backup
cp app/Http/Controllers/Front/FrontendController.php app/Http/Controllers/Front/FrontendController.php.bak2

# Adicionar log após pegar o $term
sed -i '/\$term = \$request->search;/a\        \\Log::info("🔍 BUSCA - Parâmetros recebidos", ["term" => $term, "category_id" => $category_id, "all_params" => $request->all()]);' app/Http/Controllers/Front/FrontendController.php

echo "✅ Logs adicionados!"
