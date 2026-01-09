#!/bin/bash
echo "=== Verificando propriedades sem tradução completa ==="
php artisan tinker <<TINKER
\$props = \App\Models\User\Property\Property::where('user_id', 148)->where('status', 1)->with('contents')->get();
\$incomplete = 0;
\$props->each(function(\$p) use (&\$incomplete) {
    \$langs = \$p->contents->pluck('language_id')->toArray();
    \$missing = array_diff([176, 178, 179], \$langs);
    if (!empty(\$missing)) {
        \$incomplete++;
        \$langNames = [176 => 'EN', 178 => 'ES', 179 => 'PT'];
        \$missingNames = array_map(fn(\$id) => \$langNames[\$id], \$missing);
        echo "❌ ID {\$p->id}: Faltam " . implode(', ', \$missingNames) . "\n";
    }
});
if (\$incomplete == 0) {
    echo "✅ Todas as propriedades têm PT, EN e ES!\n";
} else {
    echo "\n⚠️  Total com problema: {\$incomplete} propriedades\n";
}
exit
TINKER
