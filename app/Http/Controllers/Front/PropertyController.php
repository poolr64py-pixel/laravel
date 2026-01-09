<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\Property\Property;
use App\Models\User\Property\PropertyContent;
use App\Models\User\Property\City;
use App\Models\BasicSettings\Basic\Seo;

class PropertyController extends Controller
{
    /**
     * Catálogo completo de imóveis com busca e filtros
     */
    public function index(Request $request)
    {
      // Pegar idioma ativo
        $lang_code = session('frontend_lang', 'pt');
        $lang_id = $lang_code == 'pt' ? 179 : ($lang_code == 'en' ? 176 : 178);
        // Query base - APENAS user_id = 148
        $propertyQuery = Property::query()
            ->where('status', 1)
            ->where('user_id', 148) // Apenas seus imóveis
            ->whereHas('contents', function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            });

        // 🔍 BUSCA POR TEXTO
        if ($request->filled('q')) {
            $search = $request->q;
            $propertyQuery->where(function($query) use ($search, $lang_id) {
                $query->whereHas('contents', function($q) use ($search, $lang_id) {
                    $q->where('language_id', $lang_id)
                      ->where(function($sq) use ($search) {
                          $sq->where('title', 'like', "%{$search}%")
                             ->orWhere('description', 'like', "%{$search}%")
                             ->orWhere('address', 'like', "%{$search}%");
                      });
                })
                ->orWhereHas('city.cityContent', function($q) use ($search, $lang_id) {
                    $q->where('language_id', $lang_id)
                      ->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 🏠 FILTRO POR TIPO
        if ($request->filled('type')) {
            $propertyQuery->where('type', $request->type);
        }

        // 🎯 FILTRO POR FINALIDADE
        if ($request->filled('purpose')) {
            $propertyQuery->where('purpose', $request->purpose);
        }

        // 🏙️ FILTRO POR CIDADE
        if ($request->filled('city_id')) {
            $propertyQuery->where('city_id', $request->city_id);
        }

        // 🛏️ FILTRO POR QUARTOS
        if ($request->filled('beds')) {
            $propertyQuery->where('beds', '>=', $request->beds);
        }

        // 🛁 FILTRO POR BANHEIROS
        if ($request->filled('bath')) {
            $propertyQuery->where('bath', '>=', $request->bath);
        }

        // 💰 FILTRO POR PREÇO
        if ($request->filled('price_min')) {
            $propertyQuery->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $propertyQuery->where('price', '<=', $request->price_max);
        }

        // 📐 FILTRO POR ÁREA
        if ($request->filled('area_min')) {
            $propertyQuery->where('area', '>=', $request->area_min);
        }
        if ($request->filled('area_max')) {
            $propertyQuery->where('area', '<=', $request->area_max);
        }

        // 📊 ORDENAÇÃO
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $propertyQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $propertyQuery->orderBy('price', 'desc');
                break;
            case 'oldest':
                $propertyQuery->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                $propertyQuery->orderBy('created_at', 'desc');
                break;
        }

        // Resultado final
        $data['properties'] = $propertyQuery
            ->with([
                'contents' => function($q) use ($lang_id) {
                    $q->where('language_id', $lang_id);
                },
                'city.cityContent' => function($q) use ($lang_id) {
                    $q->where('language_id', $lang_id);
                },
                'user'
            ])
            ->paginate(12)
            ->appends($request->all());

        // Cidades para filtro
        $data['cities'] = City::with(['cityContent' => function($q) use ($lang_id) {
            $q->where('language_id', $lang_id);
        }])->get();

        $data['total_properties'] = $data['properties']->total();
        $data['seo'] = Seo::where('language_id', $lang_id)->first();
        $data['request'] = $request;

        return view('front.properties.index', $data);
    }

    /**
     * Detalhes de um imóvel específico
     */
    public function show($slug)
    {
        $lang_id = 179; // Português

        // Buscar pelo slug
        $propertyContent = PropertyContent::where('slug', $slug)
            ->where('language_id', $lang_id)
            ->firstOrFail();

        $data['property'] = Property::with([
            'contents' => function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            },
            'city.cityContent' => function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            },
            'state.stateContent' => function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            },
            'country.countryContent' => function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            },
            'amenities',
            'sliderImages',
            'user'
        ])->findOrFail($propertyContent->property_id);
        
        // Adicionar nomes para facilitar na view
        $data['property']->city_name = $data['property']->city?->cityContent->first()?->name;
        $data['property']->state_name = $data['property']->state?->stateContent->first()?->name;
        $data['property']->country_name = $data['property']->country?->countryContent->first()?->name;
        // Imóveis relacionados
        $data['related_properties'] = Property::query()
            ->where('status', 1)
            ->where('user_id', 148)
            ->where('id', '!=', $data['property']->id)
            ->where(function($q) use ($data) {
                $q->where('city_id', $data['property']->city_id)
                  ->orWhere('type', $data['property']->type);
            })
            ->whereHas('contents', function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            })
            ->with(['contents' => function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            }])
            ->limit(3)
            ->get();

        $data['seo'] = Seo::where('language_id', $lang_id)->first();

        return view('front.properties.show', $data);
    }
}
