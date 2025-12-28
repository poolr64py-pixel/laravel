#!/bin/bash

# Adicionar log DEPOIS da query para ver os resultados
sed -i '/\$data\['\''blogs'\''\] = Blog::when/a\        \\Log::info("🔍 BLOG Query Results", ["total" => $data["blogs"]->total(), "ids" => $data["blogs"]->pluck("id")->toArray(), "user_ids" => $data["blogs"]->pluck("user_id")->unique()->toArray()]);' app/Http/Controllers/Front/FrontendController.php

echo "✅ Log de resultados adicionado!"
