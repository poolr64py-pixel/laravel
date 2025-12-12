<?php

namespace App\Http\Controllers\Admin;

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
        $allowedExts = array('jpg', 'png', 'jpeg');

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
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }


        $input = $request->all();

        $input['bcategory_id'] = $request->category;
        $input['language_id'] = $request->language;
        $input['slug'] = $slug;

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $img->getClientOriginalExtension();
            $request->session()->put('blog_image', $filename);
            $request->file('image')->move(public_path('assets/front/img/blogs/'), $filename);
            $input['main_image'] = $filename;
        }
        $input['content'] = Purifier::clean($request->content);

        $blog = new Blog;

        $blog->create($input);

        Session::flash('success', __('Added successfully!'));
        return "success";
    }

    public function update(Request $request)
    {
        $img = $request->file('image');
        $allowedExts = array('jpg', 'png', 'jpeg');

        $slug = make_slug($request->title);
        $blog = Blog::findOrFail($request->blog_id);

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
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
        $input = $request->all();
        $blog = Blog::findOrFail($request->blog_id);

        $input['bcategory'] = $request->category;
        $input['slug'] = $slug;

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $img->getClientOriginalExtension();
            $request->file('image')->move('assets/front/img/blogs/', $filename);
            @unlink(public_path('assets/front/img/blogs/' . $blog->main_image));
            $input['main_image'] = $filename;
        }
        $input['content'] = Purifier::clean($request->content);


        $blog->update($input);

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

    }
