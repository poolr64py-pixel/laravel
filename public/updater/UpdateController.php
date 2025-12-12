<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class UpdateController extends Controller
{
    public function version()
    {
        return view('updater.version');
    }

    public function recurse_copy($src, $dst)
    {
        // dd(base_path($src), base_path($dst));
        $dir = opendir(base_path($src));
        @mkdir(base_path($dst), 0775, true);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir(base_path($src) . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy(base_path($src . '/' . $file), base_path($dst) . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function upversion(Request $request)
    {

        $assets = array(
            ['path' => 'app', 'type' => 'folder', 'action' => 'replace'],
            ['path' => 'database/migrations', 'type' => 'folder', 'action' => 'replace'],
            ['path' => 'resources/views', 'type' => 'folder', 'action' => 'replace'],
            ['path' => 'routes', 'type' => 'folder', 'action' => 'replace'],

            ['path' => 'version.json', 'type' => 'file', 'action' => 'replace'],

            ['path' => 'assets/admin/css/custom.css', 'type' => 'file', 'action' => 'replace'],
            ['path' => 'assets/admin/js/packages.js', 'type' => 'file', 'action' => 'replace'],
            ['path' => 'assets/admin/js/edit-package.js', 'type' => 'file', 'action' => 'replace'],
            ['path' => 'assets/admin/js/dashboard.js', 'type' => 'file', 'action' => 'replace'],

            ['path' => 'assets/tenant/js/ai-content-image-generator.js', 'type' => 'file', 'action' => 'add'],

        );
        foreach ($assets as $key => $asset) {
            $des = '';
            if (strpos($asset["path"], 'assets/') !== false) {
                $des = 'public/' . $asset["path"];
            } else {
                $des = $asset["path"];
            }
            // if updater need to replace files / folder (with/without content)
            if ($asset['action'] == 'replace') {
                if ($asset['type'] == 'file') {
                    copy(base_path('public/updater/' . $asset["path"]), base_path($des));
                }
                if ($asset['type'] == 'folder') {
                    $this->delete_directory(base_path($des));
                    $this->recurse_copy('public/updater/' . $asset["path"], $des);
                }
            }
            // if updater need to add files / folder (with/without content)
            elseif ($asset['action'] == 'add') {

                if ($asset['type'] == 'file') {
                    if (!file_exists(base_path($asset["path"]))) {
                        copy(base_path('public/updater/' . $asset["path"]), base_path($des));
                    }
                }

                if ($asset['type'] == 'folder') {

                    $this->recurse_copy('public/updater/' . $asset["path"], $des);
                }
            }
        }

        $arr = ['WEBSITE_HOST' => $request->website_host];
        setEnvironmentValue($arr);

        // Add AI columns to basic_settings table
        Schema::table('basic_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('basic_settings', 'ai_generate_status')) {
                $table->tinyInteger('ai_generate_status')
                    ->after('about_additional_section_status')
                    ->unsigned()
                    ->nullable()
                    ->comment('1 for active, 0 for deactive');
            }
            if (!Schema::hasColumn('basic_settings', 'gemini_apikey')) {
                $table->string('gemini_apikey')
                    ->after('ai_generate_status')
                    ->nullable();
            }
            if (!Schema::hasColumn('basic_settings', 'gemini_model')) {
                $table->string('gemini_model')
                    ->default('gemini-2.5-flash')
                    ->after('gemini_apikey')
                    ->nullable();
            }
        });

        Artisan::call('migrate', [
            '--force' => true
        ]);


        $languages = Language::get();

        //Adding new keywords to all language files for admin website
        $newKeys = [
            "AI Content Generation" => "AI Content Generation",
            "Unlimited Tokens" => "Unlimited Tokens",
            "Token" => "Token",
            "Tokens" => "Tokens",
            "Available for Property & Project Creation" => "Available for Property & Project Creation",
            "Powered by Google Gemini" => "Powered by Google Gemini",
            "AI Image Generation" => "AI Image Generation",
            "Unlimited Images" => "Unlimited Images",
            "Powered by Pollinations.ai Flux Model" => "Powered by Pollinations.ai Flux Model"
        ];

        foreach ($languages as $language) {

            $jsonData = file_get_contents(resource_path('lang/') . $language->code . '.json');
            $keywords = json_decode($jsonData, true);
            $datas = array_merge($newKeys, $keywords);
            $jsonData = json_encode($datas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $fileLocated = resource_path('lang/') . $language->code . '.json';
            file_put_contents($fileLocated, $jsonData);
        }
        // added keyword for default json
        $defaultjsonData = file_get_contents(resource_path('lang/') . 'default.json');
        $defaultkeywords = json_decode($defaultjsonData, true);
        $defaultdatas = array_merge($newKeys, $defaultkeywords);
        $defaultjsonData = json_encode($defaultdatas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $fileLocated = resource_path('lang/') . 'default.json';
        file_put_contents($fileLocated, $defaultjsonData);


        //Adding new keywords to all language files for admin Dashboard
        $adminDashboardnewKeys = [
            "AI Content Generation"                                      => "AI Content Generation",
            "AI Content Generation via Google Gemini"                    => "AI Content Generation via Google Gemini",
            "AI Image Generation"                                        => "AI Image Generation",
            "AI Image Generation via Pollinations.ai Flux Model"         => "AI Image Generation via Pollinations.ai Flux Model",
            "AI Powered Features"                                        => "AI Powered Features",
            "AI Technology Powered by"                                   => "AI Technology Powered by",
            "AI features are available only for Property and Project creation" => "AI features are available only for Property and Project creation",
            "Advanced AI capabilities to enhance content creation and productivity" => "Advanced AI capabilities to enhance content creation and productivity",
            "Note"                                                       => "Note",
            "Token-based"                                                => "Token-based",
            "Unlimited Free"                                             => "Unlimited Free",
            "Powered by Advanced AI"                                     => "Powered by Advanced AI",
            "Content Generation powered by Google Gemini"                => "Content Generation powered by Google Gemini",
            "Image Generation powered by Pollinations.ai Flux Model"     => "Image Generation powered by Pollinations.ai Flux Model",
            "Number of AI Content Tokens"                                => "Number of AI Content Tokens",
            "Enter number of AI content generation tokens"               => "Enter number of AI content generation tokens",
            "Token Information"                                          => "Token Information",
            "1 token = approximately 4 characters"                      => "1 token = approximately 4 characters",
            "Enter 999999 for unlimited tokens"                          => "Enter 999999 for unlimited tokens",
            "Recommended: 10,000 - 50,000 tokens per package"            => "Recommended: 10,000 - 50,000 tokens per package",
            "ai_tokens"                                                  => "ai_tokens",
            "Gemini AI"                                                  => "Gemini AI",
            "Info"                                                       => "Info",
            "Gemini Model"                                               => "Gemini Model",
            "Gemini 2.5 Flash"                                           => "Gemini 2.5 Flash",
            "Fast & Recommended"                                         => "Fast & Recommended",
            "Gemini 2.5 Pro"                                             => "Gemini 2.5 Pro",
            "Advanced Reasoning"                                         => "Advanced Reasoning",
            "Gemini 2.5 Flash-Lite"                                      => "Gemini 2.5 Flash-Lite",
            "Cost Efficient"                                             => "Cost Efficient",
            "Gemini 2.0 Flash"                                           => "Gemini 2.0 Flash",
            "Stable"                                                     => "Stable",
            "Select AI model based on your needs. Flash is recommended for general content generation" => "Select AI model based on your needs. Flash is recommended for general content generation",
            "API Key"                                                    => "API Key",
            "This API key is used only for AI content generation. Get your API key from" => "This API key is used only for AI content generation. Get your API key from",
            "Google AI Studio"                                           => "Google AI Studio",
            "Gemini AI Information"                                      => "Gemini AI Information",
            "API Key Usage"                                              => "API Key Usage",
            "Gemini API key will be used exclusively for generating content such as title, descriptions and other text-based materials" => "Gemini API key will be used exclusively for generating content such as title, descriptions and other text-based materials",
            "Supported Content Types:"                                   => "Supported Content Types:",
            "Property and Project titles and Description"                => "Property and Project titles and Description",
            "Meta Keywords and descriptions"                             => "Meta Keywords and descriptions",
            "Required AI Tokens"                                         => "Required AI Tokens",
            "All Tenants"                                                => "All Tenants",
            "This shows the total number of AI tokens you need to purchase to cover all active tenants" => "This shows the total number of AI tokens you need to purchase to cover all active tenants",
            "It is calculated as: AI tokens per pricing plan × number of active subscriptions for that plan (summed for all plans)" => "It is calculated as: AI tokens per pricing plan × number of active subscriptions for that plan (summed for all plans)",
            "This is the allocated token requirement, not the tokens already used" => "This is the allocated token requirement, not the tokens already used",

            "Remaining AI Tokens"                                        => "Remaining AI Tokens",
            "This shows how many AI tokens are still available for all tenants after deducting the used tokens from the total allocated tokens" => "This shows how many AI tokens are still available for all tenants after deducting the used tokens from the total allocated tokens",
            "Remaining Tokens = Required Tokens − Used Tokens"           => "Remaining Tokens = Required Tokens − Used Tokens",
            "This indicates the unused token balance that is still safe to use" => "This indicates the unused token balance that is still safe to use",

            "Used AI Tokens"                                             => "Used AI Tokens",
            "This shows how many AI tokens have already been consumed by all tenants who are subscribed to AI content generation plans" => "This shows how many AI tokens have already been consumed by all tenants who are subscribed to AI content generation plans",
            "It includes token usage from all AI-enabled pricing plans and all their users/tenants" => "It includes token usage from all AI-enabled pricing plans and all their users/tenants",
            "This is the total used tokens, not the remaining or required tokens" => "This is the total used tokens, not the remaining or required tokens"
        ];

        foreach ($languages as $language) {

            $jsonData = file_get_contents(resource_path('lang/') . 'admin_' . $language->code . '.json');
            $keywords = json_decode($jsonData, true);
            $datas = array_merge($adminDashboardnewKeys, $keywords);
            $jsonData = json_encode($datas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $fileLocated = resource_path('lang/') . 'admin_' . $language->code . '.json';
            file_put_contents($fileLocated, $jsonData);
        }

        // added keyword for default json
        $adminDefaultjsonData = file_get_contents(resource_path('lang/') . 'admin_default.json');
        $adminDefaultkeywords = json_decode($adminDefaultjsonData, true);
        $adminDefaultdatas = array_merge($adminDashboardnewKeys, $adminDefaultkeywords);
        $adminDefaultjsonData = json_encode($adminDefaultdatas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $adminFileLocated = resource_path('lang/') . 'admin_default.json';
        file_put_contents($adminFileLocated, $adminDefaultjsonData);



        //Adding new keywords to all language files for tenant Dashboard
        $adminDashboardnewKeys = [
            "Generate"                                           => "Generate",
            "Generate All Content"                               => "Generate All Content",
            "Generate Content with AI"                           => "Generate Content with AI",
            "Generate in Language"                               => "Generate in Language",
            "Language"                                           => "Language",
            "What kind of content do you want to generate?"      => "What kind of content do you want to generate?",
            "A luxury 3-bedroom apartment in the city center with a great view" => "A luxury 3-bedroom apartment in the city center with a great view",
            "Category"                                           => "Category",
            "Select Category"                                    => "Select Category",
            "Country"                                            => "Country",
            "Select Country"                                     => "Select Country",
            "Generate Images with AI"                            => "Generate Images with AI",
            "Describe Your Image Idea"                           => "Describe Your Image Idea",
            "A modern living room with a view of the city at sunset" => "A modern living room with a view of the city at sunset",
            "Art Style"                                          => "Art Style",
            "Photorealistic"                                     => "Photorealistic",
            "Interior Design"                                    => "Interior Design",
            "Architecture"                                       => "Architecture",
            "3D Render"                                          => "3D Render",
            "Lighting"                                           => "Lighting",
            "Natural Light"                                      => "Natural Light",
            "Cinematic"                                          => "Cinematic",
            "Studio"                                             => "Studio",
            "Golden Hour"                                        => "Golden Hour",
            "Blue Hour"                                          => "Blue Hour",
            "Camera Angle"                                       => "Camera Angle",
            "Eye-level"                                          => "Eye-level",
            "Low Angle"                                          => "Low Angle",
            "High Angle"                                         => "High Angle",
            "Aerial View"                                        => "Aerial View",
            "Wide Shot"                                          => "Wide Shot",
            "Image Size"                                         => "Image Size",
            "Square (1024x1024)"                                 => "Square (1024x1024)",
            "Landscape (1792x1024)"                              => "Landscape (1792x1024)",
            "Portrait (1024x1792)"                               => "Portrait (1024x1792)",
            "Number of Images"                                   => "Number of Images",
            "Generate Images"                                    => "Generate Images",
            "Content generated successfully"                     => "Content generated successfully",
            "Please purchase a package to use AI features"      => "Please purchase a package to use AI features",
            "You have exhausted your AI tokens"                  => "You have exhausted your AI tokens",
            "Please upgrade your package"                        => "Please upgrade your package",
            "You have"                                           => "You have",
            "tokens left. This action may require approximately" => "tokens left. This action may require approximately",
            "tokens"                                             => "tokens",
            "Token Left"                                         => "Token Left",
            "AI tokens are used for content generation"          => "AI tokens are used for content generation",
            "Available for Property & Project creation only"     => "Available for Property & Project creation only",
            "Please enter a description for your image"          => "Please enter a description for your image",
            "floor_planning_gallery"                             => "Floor Planning Gallery",
            "Gallery"                                            => "Gallery",
            "Thumbnail"                                          => "Thumbnail",
            "Video Poster"                                       => "Video Poster",
            "Floor Plan"                                         => "Floor Plan",
            "Generating Your Images"                             => "Generating Your Images",
            "Please wait while AI creates your project images"  => "Please wait while AI creates your project images",
            "Please wait while AI creates your property images" => "Please wait while AI creates your property images",
            "Processing"                                         => "Processing",
            "images"                                             => "images",
            "Tip: Use specific descriptions for better results" => "Tip: Use specific descriptions for better results",
            "Cancel Generation"                                  => "Cancel Generation",
            "Initializing"                                       => "Initializing",
            "Finalizing"                                         => "Finalizing",
            "Thumbnail image applied successfully"               => "Thumbnail image applied successfully",
            "Floor plan image applied successfully"             => "Floor plan image applied successfully",
            "Video poster image applied successfully"            => "Video poster image applied successfully",
            "Image(s)"                                           => "Image(s)",
            "Confirm Selection"                                  => "Confirm Selection",
            "Image removed from selection"                       => "Image removed from selection",
            "Image added to selection"                           => "Image added to selection",
            "Select"                                             => "Select",
            "Success"                                            => "Success",
            "Info"                                               => "Info",
            "successfully"                                       => "successfully",
            "image(s) added to"                                  => "image(s) added to",
            "Close Gallery"                                      => "Close Gallery",
            "Use This Image"                                     => "Use This Image",
            "Generated Images - Select Multiple Images for Gallery" => "Generated Images - Select Multiple Images for Gallery",
            "Generated Image - Click to Use"                     => "Generated Image - Click to Use",
            "Floor Planning Gallery"                             => "Floor Planning Gallery",
            "Image"                                              => "Image",
            "Title"                                              => "Title",
            "Error"                                              => "Error",
            "Description"                                        => "Description",
            "Meta Keywords"                                      => "Meta Keywords",
            "Meta Description"                                   => "Meta Description",
            "Image generated successfully"                       => "Image generated successfully",
            "images generated successfully"                      => "images generated successfully",
            "Selected"                                           => "Selected",
            "Add to Gallery"                                     => "Add to Gallery",
            "AI Token"                                           => "AI Token",
            "AI Tokens"                                          => "AI Tokens",
            "Available for Property & Project Creation"         => "Available for Property & Project Creation",
            "Powered by Google Gemini"                           => "Powered by Google Gemini",
            "Unlimited Images"                                   => "Unlimited Images",
            "Powered by Pollinations.ai Flux Model"              => "Powered by Pollinations.ai Flux Model",
            "AI Image Generation"                                => "AI Image Generation",
            "AI Content Generation"                              => "AI Content Generation"
        ];

        foreach ($languages as $language) {

            $jsonData = file_get_contents(resource_path('lang/') . 'tenant_' . $language->code . '.json');
            $keywords = json_decode($jsonData, true);
            $datas = array_merge($adminDashboardnewKeys, $keywords);
            $jsonData = json_encode($datas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $fileLocated = resource_path('lang/') . 'tenant_' . $language->code . '.json';
            file_put_contents($fileLocated, $jsonData);
        }
        // added keyword for default json
        $adminDefaultjsonData = file_get_contents(resource_path('lang/') . 'tenant_default.json');
        $adminDefaultkeywords = json_decode($adminDefaultjsonData, true);
        $adminDefaultdatas = array_merge($adminDashboardnewKeys, $adminDefaultkeywords);
        $adminDefaultjsonData = json_encode($adminDefaultdatas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $adminFileLocated = resource_path('lang/') . 'tenant_default.json';
        file_put_contents($adminFileLocated, $adminDefaultjsonData);

        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        Session::flash('success', 'Updated successfully');
        return redirect('updater/success.php');
    }

    function delete_directory($dirname)
    {
        $dir_handle = null;
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file))
                    unlink($dirname . "/" . $file);
                else
                    $this->delete_directory($dirname . '/' . $file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    public function redirectToWebsite(Request $request)
    {
        $arr = ['WEBSITE_HOST' => $request->website_host];
        setEnvironmentValue($arr);
        Artisan::call('config:clear');

        return redirect()->route('front.index');
    }
}
