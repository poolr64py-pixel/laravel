<?php

namespace App\Http\Controllers\Admin;

use App\Services\TranslationService;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bcategory;
use App\Models\Language;
use App\Models\Blog;
use App\Traits\AdminLanguage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;

class BlogController extends Controller
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
        $data['lang_id'] = $lang_id;
        $data['blogs'] = Blog::where('language_id', $lang_id)->orderBy('serial_number', 'asc')->get();
        $data['bcats'] = Bcategory::where('language_id', $lang_id)->where('status', 1)->orderBy('serial_number', 'asc')->get();
        $data['langs'] = Language::all();

        return view('admin.blog.blog.index', $data);
    }

    public function edit($id)
    {
        $data['blog'] = Blog::findOrFail($id);
        $data['bcats'] = Bcategory::where('language_id', $data['blog']->language_id)->where('status', 1)->get();
        $data['langs'] = Language::all();
        return view('admin.blog.blog.edit', $data);
    }


    public function store(Request $request)
    {
        $img = $request->file('image');
        $allowedExts = array('jpg', 'png', 'jpeg', 'webp');
        $slug = make_slug($request->title);
        
        $rules = [
            'language' => 'required',
            'title' => 'required|max:255',
            'category' => 'required',
            'content' => 'required',
            'serial_number' => 'required|integer',
            'image' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    if (!empty($img)) {
                        $ext = $img->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    }
                },
            ],
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $input = $request->all();
        $input['bcategory_id'] = $request->category;
        $lang = Language::where('code', $request->language)->first();
        $input['language_id'] = $lang ? $lang->id : $request->language;
        $input['slug'] = $slug;
        
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $filename = time() . '.' . $img->getClientOriginalExtension();
            $request->file('image')->move(public_path('assets/front/img/blogs/'), $filename);
            // Converter para WebP
            $sourcePath = public_path("assets/front/img/blogs/" . $filename);
            $webpFilename = time() . ".webp";
            $webpPath = public_path("assets/front/img/blogs/" . $webpFilename);
            
            // Detectar tipo real do arquivo (não confiar apenas na extensão)
            $imageInfo = getimagesize($sourcePath);
            $mimeType = $imageInfo['mime'] ?? null;
            
            if ($mimeType == 'image/jpeg') {
                $image = imagecreatefromjpeg($sourcePath);
            } elseif ($mimeType == 'image/png') {
                $image = imagecreatefrompng($sourcePath);
            } elseif ($mimeType == 'image/gif') {
                $image = imagecreatefromgif($sourcePath);
            }
            
            if (isset($image)) {
                imagewebp($image, $webpPath, 80);
                imagedestroy($image);
                @unlink($sourcePath);
                $filename = $webpFilename;
            }
            $input['main_image'] = $filename;
        }
        
        $input['content'] = Purifier::clean($request->content);
        unset($input['image']);
        
                $blog = new Blog;
        $blog->create($input);
        
        // AUTO-TRADUÇÃO: Criar versões em outros idiomas
        try {
            $this->autoTranslateBlog($blog);
        } catch (\Exception $e) {
            \Log::error('Auto-translation failed: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.blog.index', ['language' => $request->language])->with('success', __('Added successfully!'));
        return redirect()->route('admin.blog.index', ['language' => $request->language])->with('success', __('Added successfully!'));
    }


    public function update(Request $request)
    {

        $img = $request->file('image');
        $allowedExts = array('jpg', 'png', 'jpeg', 'webp');
        $slug = make_slug($request->title);
        $blog = Blog::findOrFail($request->blog_id);
        
        // DEBUG: Ver o que está sendo enviado
        \Log::info("Update Request Data:", $request->all());
        \Log::info("Category value:", ["category" => $request->category]);
        
        $rules = [
            'title' => 'required|max:255',
            'category' => 'required',
            'content' => 'required',
            'serial_number' => 'required|integer',
            'image' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    if (!empty($img)) {
                        $ext = $img->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    }
                },
            ],
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        
        $input = $request->all();
        $input['bcategory_id'] = $request->category;
        $input['slug'] = $slug;
        
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $filename = time() . '.' . $img->getClientOriginalExtension();
            $request->file('image')->move(public_path('assets/front/img/blogs/'), $filename);
            // Converter para WebP
            $sourcePath = public_path("assets/front/img/blogs/" . $filename);
            $webpFilename = time() . ".webp";
            $webpPath = public_path("assets/front/img/blogs/" . $webpFilename);
            
            // Detectar tipo real do arquivo (não confiar apenas na extensão)
            $imageInfo = getimagesize($sourcePath);
            $mimeType = $imageInfo['mime'] ?? null;
            
            if ($mimeType == 'image/jpeg') {
                $image = imagecreatefromjpeg($sourcePath);
            } elseif ($mimeType == 'image/png') {
                $image = imagecreatefrompng($sourcePath);
            } elseif ($mimeType == 'image/gif') {
                $image = imagecreatefromgif($sourcePath);
            }
            
            if (isset($image)) {
                imagewebp($image, $webpPath, 80);
                imagedestroy($image);
                @unlink($sourcePath);
                $filename = $webpFilename;
            }
            @unlink(public_path('assets/front/img/blogs/' . $blog->main_image));
            $input['main_image'] = $filename;
        } else {
            unset($input['main_image']);
        }
       $input['content'] = Purifier::clean($request->content);
        unset($input['image']);
        $blog->update($input);
        
        // AUTO-TRADUÇÃO: Atualizar traduções existentes (em background)
        try {
            // Recarregar blog para pegar dados atualizados
            $blog->refresh();
            $this->updateTranslations($blog);
        } catch (\Exception $e) {
            \Log::error('Auto-translation update failed: ' . $e->getMessage());
        }
        
        Session::flash('success', __('Updated successfully!'));
        return "success";
    }
    public function delete(Request $request)
    {

        $blog = Blog::findOrFail($request->blog_id);
        @unlink(public_path('assets/front/img/blogs/' . $blog->main_image));
        $blog->delete();

        Session::flash('success', __('Deleted successfully!'));
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $blog = Blog::findOrFail($id);
            @unlink(public_path('assets/front/img/blogs/' . $blog->main_image));
            $blog->delete();
        }

        Session::flash('success', __('Deleted successfully!'));
        return "success";
    }

    public function getcats($langid)
    {
        $bcategories = Bcategory::where('language_id', $langid)->where('status', 1)->get();

        return $bcategories;
    }
public function autoTranslate(Request $request)
    {
        $blogId = $request->blog_id;
        $targetLanguages = $request->target_languages; // Array: ['es', 'pt']
        
        $originalBlog = Blog::findOrFail($blogId);
        $fromLang = $originalBlog->language->code;
        
        $translatedCount = 0;
        
        foreach ($targetLanguages as $toLangCode) {
            $toLang = \App\Models\Language::where('code', $toLangCode)->first();
            
            if (!$toLang) continue;
            
            // Verifica se já existe tradução
            $existingTranslation = Blog::where('language_id', $toLang->id)
                ->where('slug', $originalBlog->slug)
                ->first();
                
            if ($existingTranslation) {
                continue;
            }
            
            // Traduz os campos
            $translatedTitle = \App\Helpers\AutoTranslateHelper::translate(
                $originalBlog->title, $fromLang, $toLangCode
            );
            $translatedContent = \App\Helpers\AutoTranslateHelper::translateHTML(
                $originalBlog->content, $fromLang, $toLangCode
            );
            $translatedMetaKeywords = \App\Helpers\AutoTranslateHelper::translate(
                $originalBlog->meta_keywords ?? '', $fromLang, $toLangCode
            );
            $translatedMetaDescription = \App\Helpers\AutoTranslateHelper::translate(
                $originalBlog->meta_description ?? '', $fromLang, $toLangCode
            );
            
            // Busca categoria correspondente no idioma de destino
            $targetCategory = Bcategory::where('language_id', $toLang->id)
                ->orderBy('id', 'asc')
                ->first();
            
            // Cria o novo blog traduzido
            Blog::create([
                'language_id' => $toLang->id,
                'bcategory_id' => $targetCategory ? $targetCategory->id : $originalBlog->bcategory_id,
                'title' => $translatedTitle,
                'slug' => $originalBlog->slug,
                'main_image' => $originalBlog->main_image,
                'content' => $translatedContent,
                'meta_keywords' => $translatedMetaKeywords,
                'meta_description' => $translatedMetaDescription,
                'serial_number' => $originalBlog->serial_number,
            ]);
            
            $translatedCount++;
        }
        
        Session::flash('success', __('Blog translated to') . ' ' . $translatedCount . ' ' . __('languages successfully!'));
        return response()->json(['status' => 'success', 'count' => $translatedCount]);
    }
    public function translate(Request $request)
    {
        $sourceBlog = Blog::findOrFail($request->blog_id);
        $targetLangId = $request->target_language;
        
        $exists = Blog::where("language_id", $targetLangId)
            ->where("title", $sourceBlog->title)
            ->where("id", "!=", $sourceBlog->id)
            ->first();
        
        if ($exists) {
            return response()->json(["message" => "Translation already exists"], 400);
        }
        
        $sourceCat = Bcategory::find($sourceBlog->bcategory_id);
        $targetCat = Bcategory::where("language_id", $targetLangId)
            ->where("name", $sourceCat->name)
            ->first();
        
        if (!$targetCat) {
            $targetCat = Bcategory::create([
                "language_id" => $targetLangId,
                "name" => $sourceCat->name,
                "status" => $sourceCat->status,
                "serial_number" => $sourceCat->serial_number,
            ]);
        }
        
        $targetLang = Language::find($targetLangId);
        
        Blog::create([
            "language_id" => $targetLangId,
            "bcategory_id" => $targetCat->id,
            "title" => $sourceBlog->title,
            "slug" => $sourceBlog->slug . "-" . $targetLang->code,
            "main_image" => $sourceBlog->main_image,
            "content" => $sourceBlog->content,
            "meta_keywords" => $sourceBlog->meta_keywords,
            "meta_description" => $sourceBlog->meta_description,
            "serial_number" => $sourceBlog->serial_number,
        ]);
        
        return response()->json(["message" => "Post translated successfully!"]);
    }


    public function create()
    {
        $langCode = request('language');
        $lang = Language::where('code', $langCode)->first();
        
        if (!$lang) {
            return redirect()->back()->with('error', 'Idioma inválido');
        }
        
        $data['bcats'] = Bcategory::where('language_id', $lang->id)->get();
        return view('admin.blog.blog.create', $data);
    }
    /**
     * Traduzir blog automaticamente para outros idiomas
     */
    protected function autoTranslateBlog($sourceBlog)
    {
        $translator = new TranslationService();
        
        // Pegar idioma do blog original
        $sourceLang = Language::find($sourceBlog->language_id);
        
        if (!$sourceLang || $sourceLang->code !== 'pt') {
            return; // Só traduz se o original for em português
        }
        
        // Idiomas de destino
        $targetLangs = ['en', 'es'];
        
        foreach ($targetLangs as $langCode) {
            $targetLang = Language::where('code', $langCode)->first();
            
            if (!$targetLang) {
                continue;
            }
            
            // Verificar se já existe tradução
            $exists = Blog::where('language_id', $targetLang->id)
                ->where('slug', $sourceBlog->slug . '-' . $langCode)
                ->exists();
                
            if ($exists) {
                continue;
            }
            
            // Traduzir conteúdo
            $translated = $translator->translateBlog($sourceBlog, $langCode);
            
            // Criar novo blog traduzido
            // Pegar categoria equivalente no idioma de destino
            $sourceCat = Bcategory::find($sourceBlog->bcategory_id);
            $targetCat = Bcategory::where("language_id", $targetLang->id)
                ->where("name", $sourceCat->name)
                ->first();
            
            if (!$targetCat) {
                // Se não existe, criar categoria no idioma de destino
                $targetCat = Bcategory::create([
                    "language_id" => $targetLang->id,
                    "name" => $sourceCat->name,
                    "status" => $sourceCat->status,
                    "serial_number" => $sourceCat->serial_number,
                ]);
            }
            
            $newBlog = new Blog();
            $newBlog->language_id = $targetLang->id;
            $newBlog->bcategory_id = $targetCat->id;
            $newBlog->title = $translated['title'];
            $newBlog->slug = $sourceBlog->slug . '-' . $langCode;
            $newBlog->content = $translated['content'];
            $newBlog->main_image = $sourceBlog->main_image;
            $newBlog->serial_number = $sourceBlog->serial_number;
            $newBlog->meta_keywords = $translated['meta_keywords'];
            $newBlog->meta_description = $translated['meta_description'];
            $newBlog->save();
            
            \Log::info("Blog #{$sourceBlog->id} auto-translated to {$langCode} (#{$newBlog->id})");
        }
    }
    /**
     * Atualizar traduções existentes quando o blog original é editado
     */
    protected function updateTranslations($sourceBlog)
    {
        $translator = new TranslationService();
        
        // Pegar idioma do blog original
        $sourceLang = Language::find($sourceBlog->language_id);
        
        if (!$sourceLang || $sourceLang->code !== 'pt') {
            return; // Só atualiza traduções se o original for em português
        }
        
        // Idiomas de destino
        $targetLangs = ['en', 'es'];
        
        foreach ($targetLangs as $langCode) {
            $targetLang = Language::where('code', $langCode)->first();
            
            if (!$targetLang) {
                continue;
            }
            
            // Buscar tradução existente pelo slug base
            $slugBase = preg_replace('/-pt$/', '', $sourceBlog->slug);
            $translatedBlog = Blog::where('language_id', $targetLang->id)
                ->where('slug', 'like', $slugBase . '%')
                ->first();
            
            if (!$translatedBlog) {
                // Se não existe, criar nova tradução
                $translated = $translator->translateBlog($sourceBlog, $langCode);
                
                // Pegar categoria equivalente no idioma de destino
                $sourceCat = Bcategory::find($sourceBlog->bcategory_id);
                $targetCat = Bcategory::where("language_id", $targetLang->id)
                    ->where("name", $sourceCat->name)
                    ->first();
                
                if (!$targetCat) {
                    // Se não existe, criar categoria no idioma de destino
                    $targetCat = Bcategory::create([
                        "language_id" => $targetLang->id,
                        "name" => $sourceCat->name,
                        "status" => $sourceCat->status,
                        "serial_number" => $sourceCat->serial_number,
                    ]);
                }
                
                $newBlog = new Blog();
                $newBlog->language_id = $targetLang->id;
                $newBlog->bcategory_id = $targetCat->id;
                $newBlog->title = $translated['title'];
                $newBlog->slug = $slugBase . '-' . $langCode;
                $newBlog->content = $translated['content'];
                $newBlog->main_image = $sourceBlog->main_image;
                $newBlog->serial_number = $sourceBlog->serial_number;
                $newBlog->meta_keywords = $translated['meta_keywords'];
                $newBlog->meta_description = $translated['meta_description'];
                $newBlog->save();
                
                \Log::info("Created translation for blog #{$sourceBlog->id} in {$langCode} (#{$newBlog->id})");
            } else {
                // Atualizar tradução existente
                $translated = $translator->translateBlog($sourceBlog, $langCode);
                
                // Pegar categoria equivalente no idioma de destino
                $sourceCat = Bcategory::find($sourceBlog->bcategory_id);
                $targetCat = Bcategory::where("language_id", $targetLang->id)
                    ->where("name", $sourceCat->name)
                    ->first();
                
                if (!$targetCat) {
                    $targetCat = Bcategory::create([
                        "language_id" => $targetLang->id,
                        "name" => $sourceCat->name,
                        "status" => $sourceCat->status,
                        "serial_number" => $sourceCat->serial_number,
                    ]);
                }
                
                $translatedBlog->title = $translated['title'];
                $translatedBlog->content = $translated['content'];
                $translatedBlog->bcategory_id = $targetCat->id;
                $translatedBlog->main_image = $sourceBlog->main_image;
                $translatedBlog->serial_number = $sourceBlog->serial_number;
                $translatedBlog->meta_keywords = $translated['meta_keywords'];
                $translatedBlog->meta_description = $translated['meta_description'];
                $translatedBlog->save();
                
                \Log::info("Updated translation for blog #{$sourceBlog->id} in {$langCode} (#{$translatedBlog->id})");
            }
        }
    }
}