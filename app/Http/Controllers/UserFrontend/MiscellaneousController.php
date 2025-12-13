<?php

namespace App\Http\Controllers\UserFrontend;

use App\Http\Controllers\Controller;
use App\Models\User\BasicSetting;
use App\Models\User\Language;
use App\Models\User\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Session;

class MiscellaneousController extends Controller
{
    use TenantFrontendLanguage;

public function changeLanguage(Request $request)
{
    $langCode = $request->input('lang_code');
    
    error_log('🔴 changeLanguage: lang_code=' . $langCode);
    error_log('🔴 Session ID ANTES: ' . session()->getId());
    
    if (!$langCode) {
        return back();
    }
    
    $user = getUser();
    
    if (!$user) {
        error_log('🔴 Sem usuário');
        return back();
    }
    
    error_log('🔴 User ID: ' . $user->id);
    
    $language = Language::where('user_id', $user->id)
        ->where('code', $langCode)
        ->first();
    
    if (!$language) {
        error_log('🔴 Idioma não encontrado');
        return back();
    }
    
    // Salvar na sessão
    session()->put('lang', $langCode);
    session()->put('language_id', $language->id);
    session()->put('user_id', $user->id); // Salvar user_id também
    session()->save();
    
    error_log('✅ SESSÃO SALVA: lang=' . session('lang'));
    error_log('✅ Session ID DEPOIS: ' . session()->getId());
    error_log('✅ Todas chaves: ' . implode(', ', array_keys(session()->all())));
    
    return redirect()->back();
}

    public static function getBreadcrumb($tenantId)
    {
        $breadcrumb = BasicSetting::where('user_id', $tenantId)->pluck('breadcrumb')->first();

        return $breadcrumb;
    }

    public function getFrontCurrencyInfo($userId)
    {
        $baseCurrencyInfo = BasicSetting::where('user_id', $userId)->select('base_currency_symbol', 'base_currency_symbol_position', 'base_currency_text', 'base_currency_text_position', 'base_currency_rate')
            ->first();

        return $baseCurrencyInfo;
    }

    public function storeSubscriber(Request $request)
    {
        $userId = getUser()->id;
        $request->validate([
            'email' => ['required', 'email:rfc,dns',  Rule::unique('user_subscribers')->where(function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })]
        ], [
            'email.required' => 'Please enter your email address.',
            'email.unique' => 'This email address is already exist!'
        ]);

        Subscriber::query()->create([
            'user_id' => $userId,
            'email' => $request->email
        ]);
        Session::flash('success', __('You have successfully subscribed to our newsletter.'));

        return redirect()->back();
    }
}
