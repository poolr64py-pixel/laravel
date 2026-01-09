<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HeroSlider; // Modelo Eloquent para a tabela hero_sliders

class HeroSliderController extends Controller
{
    /**
     * Exibe a lista de sliders da home page.
     */
    public function sliderVersion()
    {
        $sliders = HeroSlider::orderBy('id', 'desc')->get();
        return view('user.hero.slider_version', compact('sliders'));
    }

    /**
     * Mostra o formulário para criar um novo slider.
     */
    public function createSlider()
    {
        return view('user.hero.create_slider');
    }

    /**
     * Salva as informações do slider no banco de dados.
     */
    public function storeSliderInfo(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048', // validação de imagem
        ]);

        // Faz upload da imagem
        $imagePath = $request->file('image')->store('hero_sliders', 'public');

        // Cria o slider
        HeroSlider::create([
            'title' => $request->input('title'),
            'subtitle' => $request->input('subtitle'),
            'image' => $imagePath,
        ]);

        return redirect()->route('user.home_page.hero.slider_version')
                         ->with('success', 'Slider criado com sucesso!');
    }

    /**
     * Mostra o formulário para editar um slider existente.
     */
    public function editSlider($id)
    {
        $slider = HeroSlider::findOrFail($id);
        return view('user.hero.edit_slider', compact('slider'));
    }

    /**
     * Atualiza as informações do slider existente.
     */
    public function updateSliderInfo(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $slider = HeroSlider::findOrFail($id);

        // Se uma nova imagem for enviada, substitui a antiga
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('hero_sliders', 'public');
            $slider->image = $imagePath;
        }

        $slider->title = $request->input('title');
        $slider->subtitle = $request->input('subtitle');
        $slider->save();

        return redirect()->route('user.home_page.hero.slider_version')
                         ->with('success', 'Slider atualizado com sucesso!');
    }

    /**
     * Deleta um slider existente.
     */
    public function deleteSlider(Request $request)
    {
        $slider = HeroSlider::findOrFail($request->input('id'));
        $slider->delete();

        return redirect()->route('user.home_page.hero.slider_version')
                         ->with('success', 'Slider deletado com sucesso!');
    }
}
