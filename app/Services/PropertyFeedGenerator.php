<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PropertyFeedGenerator
{
    protected Collection $properties;
    protected string $feedType; // 'brasil', 'paraguai', 'all'
    
    public function __construct(string $feedType = 'all')
    {
        $this->feedType = $feedType;
        $this->loadProperties();
    }

    /**
     * Carrega propriedades do banco
     * Adaptado para estrutura user_properties
     */
    protected function loadProperties(): void
    {
        // Query adaptada para estrutura real do terrasnoparaguay.com
        $query = DB::table('user_properties as up')
            ->join('user_property_contents as upc', 'up.id', '=', 'upc.property_id')
            ->where('upc.language_id', 176)  // Português
            ->join('users as u', 'up.user_id', '=', 'u.id')
            ->where('up.featured', 1) // Apenas imóveis em destaque
            ->where('up.approve_status', 1) // Apenas aprovados
            ->where('u.status', 1) // Apenas usuários ativos
            ->select([
                'up.id',
                'up.user_id',
                'up.price',
                'up.currency', 
                'up.beds',
                'up.bath as baths',
                'up.area',
                'up.latitude',
                'up.longitude',
                'up.featured_image',
                'up.floor_planning_image',
                'up.video_url',
                'up.video_image',
                'up.purpose',
                'up.type',
                'upc.title',
                'upc.description',
                'upc.slug',
                'upc.address',
                'u.username as owner_username',
                DB::raw("CONCAT('https://', u.username, '." . env('WEBSITE_HOST') . "/', upc.slug) as property_url")
            ]);

        // Filtrar por mercado alvo
        if ($this->feedType === 'brasil') {
            // Imóveis para investidores brasileiros
            // Critérios: maior valor, áreas nobres, ROI
            $query->where('up.price', '>=', 100000);
        } elseif ($this->feedType === 'paraguai') {
            // Imóveis para mercado local paraguaio
            // Critérios: preço acessível, localização prática
            $query->where('up.price', '<', 500000);
        }

        $this->properties = collect($query->get());
    }

    /**
     * Gera feed Google Merchant Center
     */
    public function googleMerchant(): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"></rss>');
        $channel = $xml->addChild('channel');
        
        // Título personalizado por mercado
        $titles = [
            'brasil' => 'Terras no Paraguay - Investimentos Imobiliários',
            'paraguai' => 'Terras no Paraguay - Imóveis no Paraguai',
            'all' => 'Terras no Paraguay - Imóveis',
        ];
        
        $channel->addChild('title', $titles[$this->feedType]);
        $channel->addChild('link', 'https://terrasnoparaguay.com');
        $channel->addChild('description', $this->getFeedDescription());

        foreach ($this->properties as $property) {
            $item = $channel->addChild('item');
            
            // ID único
            $this->addChildWithNamespace($item, 'g:id', "TNP_{$property->id}");
            
            // Título otimizado por mercado
            $title = $this->optimizeTitle($property);
            $this->addChildWithNamespace($item, 'g:title', $this->sanitize($title));
            
            // Descrição otimizada
            $description = $this->optimizeDescription($property);
            $this->addChildWithNamespace($item, 'g:description', $this->sanitize($description));
            
            // URLs
            $this->addChildWithNamespace($item, 'g:link', $property->property_url);
            
            // Imagem principal
            $imageUrl = $this->getImageUrl($property->featured_image, $property->owner_username);
            $this->addChildWithNamespace($item, 'g:image_link', $imageUrl);
            
            // Preço com moeda
            $formattedPrice = $this->formatPrice($property->price);
            $this->addChildWithNamespace($item, 'g:price', $formattedPrice);
            
            // Campos obrigatórios Google
            $this->addChildWithNamespace($item, 'g:condition', 'new');
            $this->addChildWithNamespace($item, 'g:availability', 'in stock');
            $this->addChildWithNamespace($item, 'g:brand', 'Terras no Paraguay');
            
            // Categorização
            $category = $this->categorizeProperty($property);
            $this->addChildWithNamespace($item, 'g:product_type', $category);
            
            // Labels customizados (para segmentação)
            $this->addChildWithNamespace($item, 'g:custom_label_0', $this->feedType); // brasil/paraguai
            $this->addChildWithNamespace($item, 'g:custom_label_1', $this->getPriceRange($property->price));
            $this->addChildWithNamespace($item, 'g:custom_label_2', $this->getPropertySize($property->beds));
            
            // Imagens adicionais (se existirem)
            if ($property->floor_planning_image) {
                $floorUrl = $this->getImageUrl($property->floor_planning_image, $property->owner_username);
                $this->addChildWithNamespace($item, 'g:additional_image_link', $floorUrl);
            }
        }

        return $xml->asXML();
    }

    /**
     * Gera feed JSON para API
     */
    public function json(): array
    {
        return [
            'meta' => [
                'total' => $this->properties->count(),
                'feed_type' => $this->feedType,
                'market' => $this->feedType === 'brasil' ? 'Investidores Brasil' : 'Compradores Paraguai',
                'generated_at' => now()->toIso8601String(),
                'version' => '1.0',
            ],
            'properties' => $this->properties->map(function ($property) {
                return [
                    'id' => "TNP_{$property->id}",
                    'title' => $this->optimizeTitle($property),
                    'description' => $property->description,
                    'price' => [
                    'amount' => $property->price,
                    'currency' => $property->currency ?? 'USD',
                    'formatted' => $this->formatPriceForDisplay($property->price, $property->currency ?? 'USD'),
                    'brl_estimate' => $this->convertToBRL($property->price, $property->currency ?? 'USD'),
                     ],
                    'location' => [
                        'address' => $property->address,
                        'coordinates' => [
                            'lat' => $property->latitude,
                            'lng' => $property->longitude,
                        ],
                    ],
                    'features' => [
                        'bedrooms' => $property->beds ?? 0,
                        'bathrooms' => $property->baths ?? 0,
                        'area' => $property->area ?? 0,
                    ],
                    'images' => [
                        'main' => $this->getImageUrl($property->featured_image, $property->owner_username),
                        'floor_plan' => $property->floor_planning_image ? $this->getImageUrl($property->floor_planning_image, $property->owner_username) : null,
                        'video_thumbnail' => $property->video_image ? $this->getImageUrl($property->video_image, $property->owner_username) : null,
                    ],
                    'video' => $property->video_url,
                    'url' => $property->property_url,
                    'owner' => [
                        'username' => $property->owner_username,
                        'profile_url' => "https://{$property->owner_username}." . env('WEBSITE_HOST'),
                    ],
                    'marketing' => [
                        'target_market' => $this->feedType,
                        'investment_angle' => $this->getInvestmentAngle($property),
                        'call_to_action' => $this->getCTA($property),
                    ],
                ];
            })->values()->toArray(),
        ];
    }

    /**
     * Otimiza título por mercado
     */
    protected function optimizeTitle($property): string
    {
        $baseTitle = $property->title;
        
        if ($this->feedType === 'brasil') {
            // Foco: Investimento, ROI, Dólar
            $suffix = sprintf(
                " - Investimento no Paraguai | %s quartos | US$ %s",
                $property->beds ?? 'N',
                number_format($property->price, 0, ',', '.')
            );
        } else {
            // Foco: Localização, Preço, Características
            $suffix = sprintf(
                " | %s dormitorios | US$ %s",
                $property->beds ?? 'N',
                number_format($property->price, 0, ',', '.')
            );
        }
        
        $title = $baseTitle . $suffix;
        
        // Google Merchant limita título a 150 caracteres
        return mb_substr($title, 0, 150);
    }

    /**
     * Otimiza descrição por mercado
     */
    protected function optimizeDescription($property): string
    {
        $baseDesc = strip_tags($property->description);
        
        if ($this->feedType === 'brasil') {
            $prefix = "💰 INVESTIMENTO NO PARAGUAI: ";
            $highlights = [
                "✅ Imóvel valorizado em dólar",
                "✅ Impostos reduzidos",
                "✅ Alto potencial de valorização",
                "✅ Possibilidade de residência"
            ];
        } else {
            $prefix = "🏡 IMÓVEL NO PARAGUAI: ";
            $highlights = [
                sprintf("✅ %s dormitórios", $property->beds ?? 'N'),
                sprintf("✅ %s m²", $property->area ?? 'N'),
                "✅ Pronto para morar",
                "✅ Documentação completa"
            ];
        }
        
        $description = $prefix . implode(" | ", $highlights) . " | " . $baseDesc;
        
        // Google Merchant limita descrição a 5000 caracteres
        return mb_substr($description, 0, 5000);
    }

    /**
     * Formata preço para Google Merchant
     */
    protected function formatPrice(?float $price): string
    {
        return number_format($price, 2, '.', '') . ' USD';
    }

    /**
     * Formata preço para exibição
     */
      protected function formatPriceForDisplay(?float $price, string $currency = 'USD'): string
    {
        if (!$price) {
        return 'Precio no disponible';
    }
        if ($currency === 'PYG') {
            return 'Gs. ' . number_format($price, 0, '.', '.');
        }
        return 'US$ ' . number_format($price, 0, ',', '.');
    }
    /**
     * Converte USD para BRL (estimativa)
     */
      protected function convertToBRL(?float $price, string $currency = 'USD'): string
    {
        $usdRate = 5.80; // Taxa USD -> BRL
        $pygRate = 0.00074; // Taxa PYG -> BRL (1 PYG ≈ 0.00074 BRL)
        
        if ($currency === 'PYG') {
            $priceBRL = $price * $pygRate;
        } else {
            $priceBRL = $price * $usdRate;
        }
        
        return 'R$ ' . number_format($priceBRL, 0, ',', '.');
    }
    /**
     * Categoriza imóvel
     */
    protected function categorizeProperty($property): string
    {
        if ($property->beds >= 4) {
            return 'Casa Grande / Investimento';
        } elseif ($property->beds >= 2) {
            return 'Casa / Apartamento';
        } else {
            return 'Studio / Apartamento Compacto';
        }
    }

    /**
     * Faixa de preço
     */
    protected function getPriceRange(?float $price): string
    {
        if ($price < 100000) return 'Até 100k';
        if ($price < 250000) return '100k-250k';
        if ($price < 500000) return '250k-500k';
        return 'Acima 500k';
    }

    /**
     * Tamanho do imóvel
     */
    protected function getPropertySize(?int $beds): string
    {
         if ($beds === null || $beds === 0) return 'Studio';  // ← ADICIONE ESTA LINHA
        if ($beds <= 1) return 'Compacto';
        if ($beds <= 2) return 'Médio';
        return 'Grande';
    }

    /**
     * URL da imagem
     */
    protected function getImageUrl(?string $image, string $username): string
    {
        if (!$image) {
            return 'https://terrasnoparaguay.com/assets/img/default-property.jpg';
        }
        
        return "https://{$username}." . env('WEBSITE_HOST') . "/assets/img/properties/{$image}";
    }

    /**
     * Ângulo de investimento
     */
    protected function getInvestmentAngle($property): string
    {
        if ($this->feedType === 'brasil') {
            $roi = round(($property->price * 0.06 / 12), 0); // 6% ao ano estimado
            return "Potencial de renda mensal: US$ {$roi} | Valorização em dólar";
        }
        
        return "Pronto para morar | Financiamento disponível";
    }

    /**
     * Call to Action
     */
    protected function getCTA($property): string
    {
        if ($this->feedType === 'brasil') {
            return "Agende uma visita virtual | WhatsApp disponível";
        }
        
        return "Agende una visita | Más información";
    }

    /**
     * Descrição do feed
     */
    protected function getFeedDescription(): string
    {
        $descriptions = [
            'brasil' => 'Oportunidades de investimento imobiliário no Paraguai para brasileiros. Imóveis valorizados em dólar com alto potencial de retorno.',
            'paraguai' => 'Imóveis à venda no Paraguai. Casas, apartamentos e terrenos com a melhor localização.',
            'all' => 'Plataforma imobiliária no Paraguai. Encontre seu imóvel ideal.',
        ];
        
        return $descriptions[$this->feedType];
    }

    /**
     * Helper para adicionar child com namespace
     */
    protected function addChildWithNamespace($parent, $name, $value)
    {
        $namespace = 'http://base.google.com/ns/1.0';
        return $parent->addChild($name, htmlspecialchars($value ?? ''), $namespace);
    }

    /**
     * Sanitiza texto para XML
     */
    protected function sanitize($text): string
    {
        return htmlspecialchars(strip_tags($text ?? ''), ENT_XML1, 'UTF-8');
    }

    /**
     * Factory methods
     */
    public static function forBrasil(): self
    {
        return new self('brasil');
    }

    public static function forParaguai(): self
    {
        return new self('paraguai');
    }

    public static function all(): self
    {
        return new self('all');
    }
}
