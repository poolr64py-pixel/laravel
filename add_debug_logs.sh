#!/bin/bash

# Fazer backup
cp app/Http/Controllers/Front/FrontendController.php app/Http/Controllers/Front/FrontendController.php.backup

# Adicionar log após detectar idioma
sed -i '/\$lang_id = \$currentLang->id;/a\        \\Log::info("🔍 BLOG Controller - Language detected", ["lang_code" => $currentLang->code, "lang_id" => $lang_id]);' app/Http/Controllers/Front/FrontendController.php

# Adicionar log antes da query
sed -i '/\$data\['\''blogs'\''\] = Blog::when/i\        \\Log::info("🔍 BLOG Controller - Before query", ["lang_id" => $lang_id, "term" => $term, "category_id" => $category_id]);' app/Http/Controllers/Front/FrontendController.php

echo "✅ Logs adicionados!"
