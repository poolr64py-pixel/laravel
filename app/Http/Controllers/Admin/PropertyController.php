<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User\Property\Property;
use App\Models\User\Property\Category;
use App\Models\User\Property\PropertyContent;
use App\Models\User\Property\City;
use App\Models\Language;
use App\Traits\AdminLanguage;
use Illuminate\Support\Facades\Session;

class PropertyController extends Controller
{
    use AdminLanguage;

    public function index(Request $request)
    {
        if ($request->has('language')) {
            $lang = $this->selectLang($request->language);
        } else {
            $lang = $this->currentLang();
        }
        
        $lang_id = $lang->id;
        
        // Buscar todos os imóveis do user_id 148
        $properties = Property::where('user_id', 148)
            ->whereHas('contents', function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            })
            ->with(['contents' => function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            }])
            ->orderBy('id', 'desc')
            ->paginate(15);
        
        $data['properties'] = $properties;
        $data['lang_id'] = $lang_id;
        $data['langs'] = Language::all();
        
        return view('admin.property.index', $data);
    }

    public function edit($id)
    {
        $property = Property::with('contents')->findOrFail($id);
        $cities = City::with('cityContent')->get();
        $langs = Language::all();
        
        return view('admin.property.edit', compact('property', 'cities', 'langs'));
    }

    public function updateStatus(Request $request)
    {
        $property = Property::findOrFail($request->property_id);
        $property->status = $request->status;
        $property->save();
        
        Session::flash('success', __('Status updated successfully!'));
        return back();
    }

    public function updateFeatured(Request $request)
    {
        $property = Property::findOrFail($request->property_id);
        $property->featured = $request->featured;
        $property->save();
        
        Session::flash('success', __('Featured status updated successfully!'));
        return back();
    }

    public function delete(Request $request)
    {
        $property = Property::findOrFail($request->property_id);
        
        // Deletar imagem
        @unlink(public_path('assets/img/property/featureds/' . $property->featured_image));
        
        // Deletar slider images
        if ($property->sliderImages) {
            foreach ($property->sliderImages as $img) {
                @unlink(public_path('assets/img/property/slider-images/' . $img->image));
            }
        }
        
        $property->delete();
        
        Session::flash('success', __('Property deleted successfully!'));
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        
        foreach ($ids as $id) {
            $property = Property::findOrFail($id);
            
            @unlink(public_path('assets/img/property/featureds/' . $property->featured_image));
            
            if ($property->sliderImages) {
                foreach ($property->sliderImages as $img) {
                    @unlink(public_path('assets/img/property/slider-images/' . $img->image));
                }
            }
            
            $property->delete();
        }
        
        Session::flash('success', __('Properties deleted successfully!'));
        return "success";
    }
public function create(Request $request)
    {
        if ($request->has('language')) {
            $lang = $this->selectLang($request->language);
        } else {
            $lang = $this->currentLang();
        }
        
        $data['langs'] = Language::all();
        $data['lang_id'] = $lang->id;
        
        // Buscar categorias, cidades, estados, países, amenidades
        $data['categories'] = \App\Models\User\Property\Category::with('categoryContent')->get();
        $data['countries'] = \App\Models\User\Property\Country::with('countryContent')->get();
        $data['amenities'] = \App\Models\User\Property\Amenity::with('amenityContent')->get();
        $data['cities'] = \App\Models\User\Property\City::with('cityContent')->get();
        
        return view('admin.property.create', $data);
    }
    


public function store(Request $request)
{
    // Validação completa
    $request->validate([
        'category_id' => 'nullable|exists:user_property_categories,id',
        'country_id' => 'nullable|exists:user_countries,id',
        'state_id' => 'nullable|exists:user_states,id',
        'city_id' => 'nullable|exists:user_cities,id',
        'price' => 'required|numeric|min:0',
        'purpose' => 'required|in:sale,rent',
        'type' => 'required|in:house,apartment,villa,office,land,commercial',
        'beds' => 'nullable|integer|min:0',
        'bath' => 'nullable|integer|min:0',
        'area' => 'nullable|numeric|min:0',
        'video_url' => 'nullable|url',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'featured' => 'nullable|boolean',
        'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
        // Validações de conteúdo multilíngue
        'title_pt' => 'required|string|max:255',
        'title_es' => 'nullable|string|max:255',
        'title_en' => 'nullable|string|max:255',
        'address_pt' => 'nullable|string|max:500',
        'address_es' => 'nullable|string|max:500',
        'address_en' => 'nullable|string|max:500',
    ]);

    $property = new Property();
    $property->user_id = 148; // Fixo para o site principal
    $property->category_id = $request->category_id ?? 1;
$property->country_id = $request->country_id ?? 1;
$property->state_id = $request->state_id ?? 1;
$property->city_id = $request->city_id ?? 1;
    $property->price = $request->price;
    $property->purpose = $request->purpose;
    $property->type = $request->type;
    $property->beds = $request->beds ?? 0;
    $property->bath = $request->bath ?? 0;
    $property->area = $request->area ?? 0;
    $property->video_url = $request->video_url;
    $property->latitude = $request->latitude ?? 0;
    $property->longitude = $request->longitude ?? 0;
    $property->status = 1;
    $property->featured = $request->has('featured') ? 1 : 0;
    $property->approve_status = 1; // Auto-aprovar para admin

    // Upload de imagem destacada
    if ($request->hasFile('featured_image')) {
        $image = $request->file('featured_image');
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        
        // Criar diretório se não existir
        $directory = public_path('assets/img/property/featureds');
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }
        
        $image->move($directory, $imageName);
        $property->featured_image = $imageName;
    }

    $property->save();

    // Salvar conteúdo em todos os idiomas
    $langs = Language::all();
    foreach ($langs as $lang) {
        $title = $request->input('title_' . $lang->code);
        
        if (!empty($title)) {
            $content = new PropertyContent();
            $content->property_id = $property->id;
            $content->language_id = $lang->id;
            $content->title = $title;
            
            // Gerar slug único
            $baseSlug = \Str::slug($title);
            $slug = $baseSlug . '-' . $lang->code;
            $count = 1;
            while (PropertyContent::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $lang->code . '-' . $count;
                $count++;
            }
            
            $content->slug = $slug;
            $content->address = $request->input('address_' . $lang->code) ?? '';
            $content->description = $request->input('description_' . $lang->code) ?? '';
            $content->meta_keyword = $request->input('meta_keyword_' . $lang->code) ?? '';
            $content->meta_description = $request->input('meta_description_' . $lang->code) ?? '';
            $content->save();
        }
    }

    Session::flash('success', __('Property created successfully!'));
    return redirect()->route('admin.property.index');
}

}
