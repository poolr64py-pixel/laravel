<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User\Project\Project;
use App\Models\User\Project\ProjectContent;
use App\Models\Language;
use App\Traits\AdminLanguage;
use Illuminate\Support\Facades\Session;

class ProjectController extends Controller
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
        
        // Buscar todos os projetos do user_id 148
        $projects = Project::where('user_id', 148)
            ->whereHas('contents', function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            })
            ->with(['contents' => function($q) use ($lang_id) {
                $q->where('language_id', $lang_id);
            }])
            ->orderBy('id', 'desc')
            ->paginate(15);
        
        $data['projects'] = $projects;
        $data['lang_id'] = $lang_id;
        $data['langs'] = Language::all();
        
        return view('admin.project.index', $data);
    }

    public function edit($id)
    {
        $project = Project::with('contents')->findOrFail($id);
        $langs = Language::all();
        
        return view('admin.project.edit', compact('project', 'langs'));
    }

    public function updateStatus(Request $request)
    {
        $project = Project::findOrFail($request->project_id);
        $project->status = $request->status;
        $project->save();
        
        Session::flash('success', __('Status updated successfully!'));
        return back();
    }

    public function delete(Request $request)
    {
        $project = Project::findOrFail($request->project_id);
        
        // Deletar imagem
        @unlink(public_path('assets/img/projects/' . $project->featured_image));
        
        $project->delete();
        
        Session::flash('success', __('Project deleted successfully!'));
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        
        foreach ($ids as $id) {
            $project = Project::findOrFail($id);
            @unlink(public_path('assets/img/projects/' . $project->featured_image));
            $project->delete();
        }
        
        Session::flash('success', __('Projects deleted successfully!'));
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
        
        return view('admin.project.create', $data);
    }
    
    public function store(Request $request)
    {
        // Validação
        $request->validate([
            'title_pt' => 'required|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $project = new Project();
        $project->user_id = 148; // Site principal
        $project->category_id = $request->category_id ?? 1;
        $project->country_id = $request->country_id ?? 1;
        $project->state_id = $request->state_id ?? 1;
        $project->city_id = $request->city_id ?? 1;
        $project->latitude = $request->latitude ?? 0;
        $project->longitude = $request->longitude ?? 0;
        $project->complete_status = 1; // 1 = completo
        $project->featured = $request->has('featured') ? 1 : 0;

        // Upload de imagem
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $directory = public_path('assets/img/projects');
            if (!file_exists($directory)) {
                mkdir($directory, 0775, true);
            }
            $image->move($directory, $imageName);
            $project->featured_image = $imageName;
        }

        $project->save();

        // Salvar conteúdo multi-idioma
        $langs = Language::all();
        foreach ($langs as $lang) {
            $title = $request->input('title_' . $lang->code);
            
            if (!empty($title)) {
                $content = new ProjectContent();
                $content->project_id = $project->id;
                $content->language_id = $lang->id;
                $content->title = $title;
                
                // Gerar slug único
                $baseSlug = \Str::slug($title);
                $slug = $baseSlug . '-' . $lang->code;
                $count = 1;
                while (ProjectContent::where('slug', $slug)->exists()) {
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

        Session::flash('success', __('Project created successfully!'));
        return redirect()->route('admin.project.index');
    }

    public function update(Request $request, $id)
    {
        // TODO: Implementar update
        Session::flash('info', __('Update not implemented yet'));
        return back();
    }
}
