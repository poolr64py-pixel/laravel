<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feeds Admin - Terras no Paraguay</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">🚀 Gerenciador de Feeds - Terras no Paraguay</h1>
            <a href="/" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">← Voltar ao Site</a>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Feed Geral</p>
                        <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['all']['meta']['total'] }}</h3>
                        <p class="text-gray-500 text-xs mt-1">Imóveis totais</p>
                    </div>
                    <div class="text-blue-600 text-5xl">📊</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Feed Brasil</p>
                        <h3 class="text-3xl font-bold text-green-600 mt-2">{{ $stats['brasil']['meta']['total'] }}</h3>
                        <p class="text-gray-500 text-xs mt-1">Investimentos (> US$ 100k)</p>
                    </div>
                    <div class="text-green-600 text-5xl">🇧🇷</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Feed Paraguai</p>
                        <h3 class="text-3xl font-bold text-orange-600 mt-2">{{ $stats['paraguai']['meta']['total'] }}</h3>
                        <p class="text-gray-500 text-xs mt-1">Compradores locais</p>
                    </div>
                    <div class="text-orange-600 text-5xl">🇵🇾</div>
                </div>
            </div>
        </div>

        <!-- URLs dos Feeds -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6">📡 URLs dos Feeds</h2>
            
            <div class="space-y-6">
                <!-- Feed Geral -->
                <div class="border-l-4 border-blue-500 pl-4 bg-blue-50 p-4 rounded">
                    <h3 class="font-bold text-lg mb-3">Feed Geral ({{ $stats['all']['meta']['total'] }} imóveis)</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold">JSON</span>
                            <code class="flex-1 bg-white px-3 py-2 rounded border text-sm">{{ url('/feeds/json') }}</code>
                            <a href="{{ url('/feeds/json') }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Abrir</a>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold">XML</span>
                            <code class="flex-1 bg-white px-3 py-2 rounded border text-sm">{{ url('/feeds/google-merchant') }}</code>
                            <a href="{{ url('/feeds/google-merchant') }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">Abrir</a>
                        </div>
                    </div>
                </div>

                <!-- Feed Brasil -->
                <div class="border-l-4 border-green-500 pl-4 bg-green-50 p-4 rounded">
                    <h3 class="font-bold text-lg mb-3">Feed Brasil - Investidores ({{ $stats['brasil']['meta']['total'] }} imóveis)</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold">JSON</span>
                            <code class="flex-1 bg-white px-3 py-2 rounded border text-sm">{{ url('/feeds/brasil/json') }}</code>
                            <a href="{{ url('/feeds/brasil/json') }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Abrir</a>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold">XML</span>
                            <code class="flex-1 bg-white px-3 py-2 rounded border text-sm">{{ url('/feeds/brasil/google-merchant') }}</code>
                            <a href="{{ url('/feeds/brasil/google-merchant') }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">Abrir</a>
                        </div>
                    </div>
                </div>

                <!-- Feed Paraguai -->
                <div class="border-l-4 border-orange-500 pl-4 bg-orange-50 p-4 rounded">
                    <h3 class="font-bold text-lg mb-3">Feed Paraguai - Compradores ({{ $stats['paraguai']['meta']['total'] }} imóveis)</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold">JSON</span>
                            <code class="flex-1 bg-white px-3 py-2 rounded border text-sm">{{ url('/feeds/paraguai/json') }}</code>
                            <a href="{{ url('/feeds/paraguai/json') }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Abrir</a>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold">XML</span>
                            <code class="flex-1 bg-white px-3 py-2 rounded border text-sm">{{ url('/feeds/paraguai/google-merchant') }}</code>
                            <a href="{{ url('/feeds/paraguai/google-merchant') }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">Abrir</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Imóveis -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6">🏠 Imóveis nos Feeds</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preço</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quartos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Feeds</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stats['all']['properties'] as $property)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $property['id'] }}</td>
                            <td class="px-6 py-4 text-sm max-w-xs truncate">{{ $property['title'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">{{ $property['price']['formatted'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $property['features']['bedrooms'] }} 🛏️</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Geral</span>
                                @if($property['price']['amount'] >= 100000)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Brasil</span>
                                @endif
                                @if($property['price']['amount'] < 500000)
                                <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Paraguai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ $property['url'] }}" target="_blank" class="text-blue-600 hover:underline font-medium">Ver Imóvel →</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ações -->
        <div class="flex gap-4">
            <a href="{{ url('/feeds/health') }}" target="_blank" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                🔍 Testar Health Check
            </a>
            <button onclick="window.location.reload()" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 font-medium">
                🔄 Atualizar Dados
            </button>
        </div>
    </div>
</body>
</html>
