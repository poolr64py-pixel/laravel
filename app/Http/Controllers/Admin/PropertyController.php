<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User\Property\Property;
use App\Models\User\Property\Category;
use App\Models\User\Property\PropertyContent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User\Property\Country;
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
        $properties = Property::with(['contents' => function($q) use ($lang_id) {
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
 $lang_id = 179; // Português   
 $property = Property::with('contents')->findOrFail($id);
    $cities = DB::table('user_cities')
    ->join('user_city_contents as cc', function($join) use ($lang_id) {
        $join->on('user_cities.id', '=', 'cc.city_id')
             ->where('cc.language_id', '=', $lang_id);
    })
    ->where('user_cities.user_id', 148)
    ->select('user_cities.id', 'cc.name')
    ->orderBy('cc.name')
    ->get();
    
    // Query direta com JOIN (igual ao create)
    $categories = DB::table('user_property_categories as cat')
        ->join('user_property_category_contents as content', function($join) use ($lang_id) {
            $join->on('cat.id', '=', 'content.category_id')
                 ->where('content.language_id', '=', $lang_id);
        })
        ->where('cat.user_id', 148)
        ->select('cat.id', 'content.name')
        ->get();
    
    $countries = DB::table('user_countries as country')
        ->join('user_country_contents as content', function($join) use ($lang_id) {
            $join->on('country.id', '=', 'content.country_id')
                 ->where('content.language_id', '=', $lang_id);
        })
        ->where('country.user_id', 148)
        ->select('country.id', 'content.name')
        ->get();
    
    $langs = Language::all();
    
       // ADICIONAR AQUI:
    $states = DB::table('user_states')
        ->join('user_state_contents as sc', function($join) use ($lang_id) {
            $join->on('user_states.id', '=', 'sc.state_id')
                 ->where('sc.language_id', '=', $lang_id);
        })
        ->where('user_states.user_id', 148)
        ->select('user_states.id', 'sc.name')
        ->orderBy('sc.name')
        ->get();
    return view('admin.property.edit', compact('property', 'states', 'cities', 'countries', 'categories', 'langs'));
}

    public function updateFeatured(Request $request)
    {
        $property = Property::findOrFail($request->property_id);
        $property->featured = $request->featured;
        $property->save();
        
        Session::flash('success', __('Featured status updated successfully!'));
        return back();
    }
    public function updateApproveStatus(Request $request)
{
    $request->validate([
        'property_id' => 'required|exists:properties,id',
        'approve_status' => 'required|in:0,1'
    ]);

    $property = Property::findOrFail($request->property_id);
    $property->approve_status = $request->approve_status;
    $property->save();

    return back()->with('success', __('Approval status updated successfully'));
}
    public function updateStatus(Request $request)
    {
        $property = Property::findOrFail($request->property_id);
        $property->status = $request->status;
        $property->save();
        
        Session::flash('success', __('Status updated successfully!'));
        return back();
    } 
   public function updateApprove(Request $request)
    {
        $property = Property::findOrFail($request->property_id);
        $property->approve_status = $request->approve_status;
        $property->save();
        
        Session::flash('success', __('Approve status updated successfully!'));
        return back();
    }
   public function deleteImage(Request $request)
{
    try {
        \Log::info('🗑️ DELETE IMAGE CHAMADO', [
            'image_id' => $request->image_id,
            'all' => $request->all()
        ]);
        
        $imageId = $request->image_id;
        
        $image = \DB::table('user_property_slider_images')
            ->where('id', $imageId)
            ->first();
        
        if ($image) {
            $imagePath = public_path('assets/img/property/slider-images/' . $image->image);
            
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
            
            \DB::table('user_property_slider_images')
                ->where('id', $imageId)
                ->delete();
        }
        
        return response()->json(['success' => true, 'message' => 'Image deleted successfully!']);
        
    } catch (\Exception $e) {
        \Log::error('❌ Erro ao deletar imagem: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
   public function update(Request $request, $id)
{
   \Log::info('🔄 UPDATE PROPERTY', [
        'id' => $id,
        'description_pt' => substr($request->input('description_pt'), 0, 100),
        'description_en' => substr($request->input('description_en'), 0, 100),
        'description_es' => substr($request->input('description_es'), 0, 100),
    ]);   
 $property = Property::findOrFail($id);
    
    // Campos básicos
    $property->price = $request->price;
    $property->currency = $request->currency ?? 'USD'; 
    $property->purpose = $request->purpose;
    $property->type = $request->type;
    $property->beds = $request->beds;
    $property->bath = $request->bath;
    $property->area = $request->area;
    $property->latitude = $request->latitude ?? $property->latitude ?? '0';
    $property->longitude = $request->longitude ?? $property->longitude ?? '0';
    $property->video_url = $request->video_url;
    $property->category_id = $request->category_id ?: 0;  // Se for null, usa 0
    $property->country_id = $request->country_id ?: 0; 
    $property->state_id = $request->state_id ?: 0;
    $property->city_id = $request->city_id ?: 0;
    $property->featured = $request->has('featured') ? 1 : 0;
    
    // Upload de imagens (código que já tinha)
    if ($request->hasFile('featured_image')) {
        if ($property->featured_image && file_exists(public_path('assets/img/property/featureds/' . $property->featured_image))) {
            @unlink(public_path('assets/img/property/featureds/' . $property->featured_image));
        }
        $image = $request->file('featured_image');
$imageName = uniqid() . '.' . $image->getClientOriginalExtension();
$image->move(public_path('assets/img/property/featureds'), $imageName);

// Converter para WebP
$imagePath = public_path('assets/img/property/featureds/' . $imageName);
$webpName = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
$webpPath = public_path('assets/img/property/featureds/' . $webpName);

exec("convert '$imagePath' -quality 80 -resize '1200x800>' '$webpPath' 2>&1");

// Se WebP foi criado com sucesso, usar ele
if (file_exists($webpPath) && filesize($webpPath) > 0) {
    @unlink($imagePath); // Deletar original
    $property->featured_image = $webpName;
} else {
    $property->featured_image = $imageName; // Fallback
}
    }
    
    if ($request->hasFile('floor_planning_image')) {
        if ($property->floor_planning_image && file_exists(public_path('assets/img/property/plannings/' . $property->floor_planning_image))) {
            @unlink(public_path('assets/img/property/plannings/' . $property->floor_planning_image));
        }
        $image = $request->file('floor_planning_image');
        $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('assets/img/property/plannings'), $imageName);
         // Otimizar imagem automaticamente
        $imagePath = public_path('assets/img/property/featureds/' . $imageName);
        exec("convert '$imagePath' -quality 75 -resize '1200x800>' '$imagePath'");

        $property->floor_planning_image = $imageName;
    }
    
    if ($request->hasFile('video_image')) {
        if ($property->video_image && file_exists(public_path('assets/img/property/video/' . $property->video_image))) {
            @unlink(public_path('assets/img/property/video/' . $property->video_image));
        }
        $image = $request->file('video_image');
        $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('assets/img/property/video'), $imageName);
         // Otimizar imagem automaticamente
        $imagePath = public_path('assets/img/property/featureds/' . $imageName);
        exec("convert '$imagePath' -quality 75 -resize '1200x800>' '$imagePath'");
        $property->video_image = $imageName;
    }
    
    $property->save();
        // Upload de galeria de imagens
    if ($request->hasFile('gallery_images')) {
        foreach ($request->file('gallery_images') as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/property/slider-images'), $imageName);
             // Otimizar imagem automaticamente
        $imagePath = public_path('assets/img/property/featureds/' . $imageName);
        exec("convert '$imagePath' -quality 75 -resize '1200x800>' '$imagePath'");

            DB::table('user_property_slider_images')->insert([
                'property_id' => $property->id,
                'user_id' => 148,
                'image' => $imageName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }  
  
    // SALVAR CONTEÚDOS MULTI-IDIOMA
    $langs = Language::all();
    foreach ($langs as $lang) {
        $code = $lang->code;
        
        $content = PropertyContent::where('property_id', $property->id)
            ->where('language_id', $lang->id)
            ->first();
            
        if (!$content) {
            $content = new PropertyContent();
            $content->property_id = $property->id;
            $content->language_id = $lang->id;
        }
        
        if ($request->input("title_{$code}")) {
            $content->title = $request->input("title_{$code}");
            $content->slug = Str::slug($request->input("title_{$code}"));
            $content->address = $request->input("address_{$code}");
            $content->description = $request->input("description_{$code}");
            $content->meta_keyword = $request->input("meta_keyword_{$code}");
            $content->meta_description = $request->input("meta_description_{$code}");
            $content->save();
        }
    }
    
    Session::flash('success', __('Property updated successfully!'));
    return redirect()->route('admin.property.index');
}
  public function delete(Request $request)
{
    $property = Property::findOrFail($request->property_id);
    
    // Deletar imagens
    if ($property->featured_image && file_exists(public_path('assets/img/property/featureds/' . $property->featured_image))) {
        @unlink(public_path('assets/img/property/featureds/' . $property->featured_image));
    }
    
    if ($property->floor_planning_image && file_exists(public_path('assets/img/property/plannings/' . $property->floor_planning_image))) {
        @unlink(public_path('assets/img/property/plannings/' . $property->floor_planning_image));
    }
    
    if ($property->video_image && file_exists(public_path('assets/img/property/video/' . $property->video_image))) {
        @unlink(public_path('assets/img/property/video/' . $property->video_image));
    }
    
    // Deletar conteúdos multi-idioma
    PropertyContent::where('property_id', $property->id)->delete();
    
    // Deletar imagens da galeria
    DB::table('user_property_slider_images')->where('property_id', $property->id)->delete();
    
    // Deletar property
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
    $lang_id = 179; // Português
   
 $cities = DB::table('user_cities')
    ->join('user_city_contents as cc', function($join) use ($lang_id) {
        $join->on('user_cities.id', '=', 'cc.city_id')
             ->where('cc.language_id', '=', $lang_id);
    })
    ->where('user_cities.user_id', 148)
    ->select('user_cities.id', 'cc.name')
    ->orderBy('cc.name')
    ->get();

    
    // Query direta com JOIN
    $categories = DB::table('user_property_categories as cat')
        ->join('user_property_category_contents as content', function($join) use ($lang_id) {
            $join->on('cat.id', '=', 'content.category_id')
                 ->where('content.language_id', '=', $lang_id);
        })
        ->where('cat.user_id', 148)
        ->select('cat.id', 'content.name')
        ->get();
    
    $countries = DB::table('user_countries as country')
        ->join('user_country_contents as content', function($join) use ($lang_id) {
            $join->on('country.id', '=', 'content.country_id')
                 ->where('content.language_id', '=', $lang_id);
        })
        ->where('country.user_id', 148)
        ->select('country.id', 'content.name')
        ->get();
    $langs = Language::all();
    
           $states = DB::table('user_states')
    ->join('user_state_contents as sc', function($join) use ($lang_id) {
        $join->on('user_states.id', '=', 'sc.state_id')
             ->where('sc.language_id', '=', $lang_id);
    })
    ->where('user_states.user_id', 148)
    ->select('user_states.id', 'sc.name')
    ->orderBy('sc.name')
    ->get();

    return view('admin.property.create', compact('states', 'cities', 'countries', 'categories', 'langs'));
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
        'currency' => 'required|in:USD,PYG',
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
    $property->currency = $request->currency ?? 'USD';
    $property->purpose = $request->purpose;
    $property->type = $request->type;
    $property->beds = $request->beds ?? 0;
    $property->bath = $request->bath ?? 0;
    $property->area = $request->area ?? 0;
    $property->video_url = $request->video_url;
    $property->latitude = $request->latitude ?? $property->latitude ?? '0';
    $property->longitude = $request->longitude ?? $property->longitude ?? '0';
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
         // Upload de galeria de imagens
    if ($request->hasFile('gallery_images')) {
        foreach ($request->file('gallery_images') as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/property/slider-images'), $imageName);
             // Otimizar imagem automaticamente
            $imagePath = public_path('assets/img/property/featureds/' . $imageName);
                  exec("convert '$imagePath' -quality 75 -resize '1200x800>' '$imagePath'");            
            DB::table('user_property_slider_images')->insert([
                'property_id' => $property->id,
                'user_id' => 148,
                'image' => $imageName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // Salvar conteúdo em todos os idiomas
    $langs = Language::all();
    foreach ($langs as $lang) {
        $title = $request->input('title_' . $lang->code);
        
        if (!empty($title)) {
            $content = new PropertyContent();
            $content->property_id = $property->id;
            $content->language_id = $lang->id;
            $content->title = $title;
            
            // Gerar slug ÚNICO (sem sufixo de idioma)
$baseSlug = \Str::slug($title);
$slug = $baseSlug;
$count = 1;
while (PropertyContent::where('slug', $slug)->where('language_id', '!=', $lang->id)->exists()) {
    $slug = $baseSlug . '-' . $count;
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
     // AJAX - Retornar estados por país
    public function getStates($country_id)
    {
        $states = \DB::table('user_states as s')
            ->join('user_state_contents as sc', 's.id', '=', 'sc.state_id')
            ->where('s.country_id', $country_id)
            ->where("s.status", 1)
            ->select("s.id", "sc.name")
            ->where('sc.language_id', 179) // PT
            ->orderBy('sc.name')
    ->get();
        
        return response()->json($states);
    }

    // AJAX - Retornar cidades por estado
    public function getCities($state_id)
    {
        $cities = \DB::table('user_cities as c')
            ->join('user_city_contents as cc', 'c.id', '=', 'cc.city_id')
            ->where('c.state_id', $state_id)
            ->where('cc.language_id', 179) // PT
            ->where('c.status', 1)
            ->select('c.id', 'cc.name')
            ->orderBy('cc.name')
            ->get();
        
        return response()->json($cities);
    }
}

