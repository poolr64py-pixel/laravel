<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\PropertyManagement\PropertyStoreRequest;
use App\Http\Requests\PropertyManagement\PropertyUpdateRequest;
use App\Jobs\NotifyUserForProperty;
use App\Models\User\Agent\Agent;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use App\Models\User\Property\Amenity;
use App\Models\User\Property\Category;
use App\Models\User\Property\City;
use App\Models\User\Property\Country;
use App\Models\User\Property\Property;
use App\Models\User\Property\PropertyAmenity;
use App\Models\User\Property\PropertyContact;
use App\Models\User\Property\PropertyContent;
use App\Models\User\Property\PropertySpecification;
use App\Models\User\Property\PropertySpecificationContent;
use App\Models\User\Property\SliderImage;
use App\Models\User\Property\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class PropertyController extends Controller
{
    use TenantFrontendLanguage;
    public function type($username, Request $request)
    {
        $data['commertialCount'] = Property::where([['type', 'commercial'], ['agent_id', Auth::guard('agent')->user()->id]])->count();
        $data['residentialCount'] = Property::where([['type', 'residential'], ['agent_id', Auth::guard('agent')->user()->id]])->count();
        return view('agent.property.type', $data);
    }

    public function index($username, Request $request)
    {
        $agent =  Auth::guard('agent')->user();
        $tenantId =  $agent->user_id;

        if ($request->has('language')) {
            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->currentLang($tenantId);
        }

        $language_id = $language->id;
        $title = null;

        if (request()->filled('title')) {
            $title = $request->title;
        }

        $data['properties'] = Property::where('agent_id', $agent->id)
            ->leftJoin('user_property_contents', 'user_properties.id', 'user_property_contents.property_id')
            ->leftJoin('user_city_contents', function ($join) use ($language_id) {
                $join->on('user_properties.city_id', '=', 'user_city_contents.city_id')
                    ->where('user_city_contents.language_id', '=', $language_id);
            })
            ->when($title, function ($query) use ($title) {
                return $query->where('user_property_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->where('user_property_contents.language_id', $language_id)
            ->select(
                'user_properties.id',
                'user_properties.type',
                'user_properties.agent_id',
                'user_properties.status',
                'user_properties.featured',
                'user_city_contents.name as city_name',
                'user_property_contents.title'
            )
            ->orderBy('user_properties.id', 'desc')
            ->paginate(10);

        return view('agent.property.index', $data);
    }

    public function create($username, Request $request)
    {
        $information = [];
        $agent = Auth::guard('agent')->user();
        $tenantId = $agent->user_id;

        $languages = $this->allLangs($tenantId);
        $information['languages'] = $languages;
        $information['defaultLang'] = $this->defaultLang($tenantId);

        if ($request->has('language')) {
            $language = $this->selectLang($tenantId, $request->language);
        } else {

            $language = $this->currentLang($tenantId);
        }
        $information['tenantLangs'] = $languages;


        $information['propertyCategories'] = Category::where([['type', $request->type], ['status', 1], ['user_id', $tenantId]])->with(['categoryContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $information['propertyCountries'] = Country::where('user_id', $tenantId)->with(['countryContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $information['states'] = State::where('user_id', $tenantId)->with(['stateContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $information['cities'] = City::where([['status', 1], ['user_id', $tenantId]])->with(['cityContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $information['amenities'] = Amenity::where('user_id', $tenantId)->with(['amenityContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->where('status', 1)->get();

        $information['tenantId'] = $tenantId;

        return view('agent.property.create', $information);
    }
    public function updateFeatured(Request $request)
    {


        return redirect()->back();
    }

    public function imagesstore($username, Request $request)
    {
        $img = $request->file('file');
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg', 'webp');
        $rules = [
            'file' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    $ext = $img->getClientOriginalExtension();
                    if (!in_array($ext, $allowedExts)) {
                        return $fail(__("Only png, jpg, jpeg images are allowed"));
                    }
                },
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
        $imageName = UploadFile::store('assets/img/property/slider-images/', $request->file('file'));

        $pi = new SliderImage();
        if (!empty($request->property_id)) {
            $pi->property_id = $request->property_id;
        }
        $pi->user_id = Auth::guard('agent')->user()->user_id;
        $pi->image = $imageName;
        $pi->save();
        return response()->json(['status' => 'success', 'file_id' => $pi->id]);
    }
    public function imagermv($username, Request $request)
    {

        $pi = SliderImage::findOrFail($request->fileid);
        $imageCount = SliderImage::where('property_id', $pi->property_id)->get()->count();
        if ($imageCount > 1) {
            @unlink(public_path('assets/img/property/slider-images/') . $pi->image);
            $pi->delete();
            return $pi->id;
        } else {
            return 'false';
        }
    }

    //imagedbrmv
    public function imagedbrmv($username, Request $request)
    {

        $pi = SliderImage::find($request->fileid);
        $imageCount = SliderImage::where('property_id', $pi->property_id)->get()->count();
        if ($imageCount > 1) {
            @unlink(public_path('assets/img/property/slider-images/') . $pi->image);
            $pi->delete();
            return $pi->id;
        } else {
            return 'false';
        }
    }
    public function store($username, PropertyStoreRequest $request)
    {

        DB::transaction(function () use ($request) {
            $agent = Agent::findorFail(Auth::guard('agent')->user()->id);

            // ========================================
            // 1. Handle Featured Image (Thumbnail)
            // ========================================
            $featuredImgName = null;

            if ($request->filled('ai_thumbnail_path')) {
                $featuredImgName = $this->moveAiImage(
                    $request->ai_thumbnail_path,
                    'assets/img/property/featureds',
                    'featured_'
                );
            } elseif ($request->hasFile('featured_image')) {
                $featuredImgName = UploadFile::store(
                    'assets/img/property/featureds',
                    $request->featured_image
                );
            }

            $tenantId = $agent->user_id;
            $languages = $this->allLangs($tenantId);

            // ========================================
            // 2. Handle Floor Plan Image
            // ========================================
            $floorPlanningImage = null;

            if ($request->filled('ai_floor_plan_path')) {
                $floorPlanningImage = $this->moveAiImage(
                    $request->ai_floor_plan_path,
                    'assets/img/property/plannings',
                    'floor_'
                );
            } elseif ($request->hasFile('floor_planning_image')) {
                $floorPlanningImage = UploadFile::store(
                    'assets/img/property/plannings',
                    $request->floor_planning_image
                );
            }

            // ========================================
            // 3. Handle Video Poster Image
            // ========================================
            $videoImage = null;

            if ($request->filled('ai_video_poster_path')) {
                $videoImage = $this->moveAiImage(
                    $request->ai_video_poster_path,
                    'assets/img/property/video',
                    'video_'
                );
            } elseif ($request->hasFile('video_image')) {
                $videoImage = UploadFile::store(
                    'assets/img/property/video',
                    $request->video_image
                );
            }

            $property = Property::create([
                'user_id' => $agent->user_id,
                'agent_id' =>  $agent->id,
                'category_id' => $request->category_id,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'featured_image' => $featuredImgName,
                'floor_planning_image' => $floorPlanningImage,
                'video_image' => $videoImage,
                'price' => $request->price,
                'purpose' => $request->purpose,
                'type' => $request->type,
                'beds' => $request->beds,
                'bath' => $request->bath,
                'area' => $request->area,
                'video_url' => $request->video_url,
                'status' => $request->status,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,

            ]);

            // ========================================
            //  Handle Gallery Images
            // ========================================

            // Regular Dropzone Uploaded Images
            $slders = $request->slider_images;
            if ($slders) {
                $pis = SliderImage::findOrFail($slders);
                foreach ($pis as $key => $pi) {
                    $pi->property_id = $property->id;
                    $pi->save();
                }
            }

            //  AI-Generated Gallery Images
            if ($request->has('ai_gallery_images')) {
                foreach ($request->ai_gallery_images as $aiImagePath) {
                    $newFileName = $this->moveAiImage(
                        $aiImagePath,
                        'assets/img/property/slider-images',
                        'gallery_'
                    );

                    if ($newFileName) {
                        SliderImage::create([
                            'property_id' => $property->id,
                            'image'       => $newFileName,
                            'user_id'     => $agent->user_id,
                        ]);
                    }
                }
            }

            if ($request->has('amenities')) {
                foreach ($request->amenities as $amenity) {
                    PropertyAmenity::create([
                        'property_id' => $property->id,
                        'amenity_id' => $amenity,
                        'user_id' => $agent->user_id,
                    ]);
                }
            }

            foreach ($languages as $language) {
                if ($request->filled($language->code . '_title') || $request->filled($language->code . '_address') || $request->filled($language->code . '_description') || $request->filled($language->code . '_meta_keyword') || $request->filled($language->code . '_meta_description')) {
                    $propertyContent = new PropertyContent();
                    $propertyContent->language_id = $language->id;
                    $propertyContent->property_id = $property->id;
                    $propertyContent->title = $request[$language->code . '_title'];
                    $propertyContent->slug = $request[$language->code . '_title'];
                    $propertyContent->address = $request[$language->code . '_address'];
                    $propertyContent->description = $request[$language->code . '_description'];
                    $propertyContent->meta_keyword = $request[$language->code . '_meta_keyword'];
                    $propertyContent->meta_description = $request[$language->code . '_meta_description'];
                    $propertyContent->save();
                }
                $label_datas = $request[$language->code . '_label'];
                foreach ($label_datas as $key => $data) {
                    if (!empty($request[$language->code . '_value'][$key])) {
                        $property_specification = PropertySpecification::where([['property_id', $property->id], ['key', $key]])->first();
                        if (is_null($property_specification)) {
                            $property_specification = new PropertySpecification();
                            $property_specification->property_id = $property->id;
                            $property_specification->key  = $key;
                            $property_specification->user_id = $agent->user_id;
                            $property_specification->save();
                        }
                        $property_specification_content = new PropertySpecificationContent();
                        $property_specification_content->language_id = $language->id;
                        $property_specification_content->property_spacification_id = $property_specification->id;
                        $property_specification_content->label = $data;
                        $property_specification_content->value = $request[$language->code . '_value'][$key];
                        $property_specification_content->save();
                    }
                }
            }
            $propertyContent = PropertyContent::where('property_id', $property->id)->select('title')->first();

            NotifyUserForProperty::dispatch($agent->user_id, $agent, $propertyContent->title);

            // ========================================
            //  Cleanup AI Temp Images
            // ========================================
            $this->cleanupAiTempImages($request);
        });
        Session::flash('success', __('Added successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    public function updateStatus($username, Request $request)
    {
        $property = Property::findOrFail($request->propertyId);

        if ($request->status == 1) {
            $property->update(['status' => 1]);
        } else {
            $property->update(['status' => 0]);
        }
        Session::flash('success', __('Updated successfully!'));

        return redirect()->back();
    }
    public function edit($usename, $id)
    {
        $tenantId = Auth::guard('agent')->user()->user_id;
        $property = Property::with('galleryImages')->where('agent_id', Auth::guard('agent')->user()->id)->findOrFail($id);
        $information['property'] = $property;
        $information['galleryImages'] = $property->galleryImages;

        $information['tenantFrontLangs'] = $this->allLangs($tenantId);

        $language = $this->defaultLang($tenantId);
        $information['language'] = $language;

        $information['propertyCategories'] = Category::where([['type', $property->type], ['status', 1], ['user_id', $tenantId]])->with(['categoryContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $information['states'] = State::getStates($tenantId, $language->id);


        $information['propertyCountries'] = Country::where('user_id', $tenantId)->get();
        $information['propertyStates'] = State::where('user_id', $tenantId)->where('country_id', $property->country_id)->get();

        $information['propertyCities'] = City::where('user_id', $tenantId)->where('state_id', $property->state_id)->get();

        $information['propertyAmenities'] = PropertyAmenity::where('property_id', $property->id)->get();
        $information['amenities'] = Amenity::with(['amenityContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->where('status', 1)->get(); 
        $information['specifications'] = PropertySpecification::where('property_id', $property->id)->get();

        return view('agent.property.edit', $information);
    }

    public function update($username, PropertyUpdateRequest $request,  $id)
    {

        DB::transaction(
            function () use ($request, $id) {
                $agent = Auth::guard('agent')->user();
                $tenantId = $agent->user_id;
                $languages = $this->allLangs($tenantId);

                $property = Property::where('agent_id', $agent->id)->findOrFail($request->property_id);


                $featuredImgName = $property->featured_image;
                $floorPlanningImage = $property->floor_planning_image;
                $videoImage = $property->video_image;

                if ($request->hasFile('featured_image')) {
                    $featuredImgName = UploadFile::update('assets/img/property/featureds/', $request->featured_image, $property->featured_image);
                }

                if ($request->hasFile('floor_planning_image')) {

                    $floorPlanningImage = UploadFile::update('assets/img/property/plannings', $request->floor_planning_image, $property->floor_planning_image);
                }
                if ($request->hasFile('video_image')) {
                    $videoImage = UploadFile::update('assets/img/property/video/', $request->video_image, $property->video_image);
                }

                $property->update([
                    'category_id' => $request->category_id,
                    'country_id' => $request->country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'featured_image' => $featuredImgName,
                    'floor_planning_image' => $floorPlanningImage,
                    'video_image' => $videoImage,
                    'price' => $request->price,
                    'purpose' => $request->purpose,
                    'type' => $request->type,
                    'beds' => $request->beds,
                    'bath' => $request->bath,
                    'area' => $request->area,
                    'video_url' => $request->video_url,
                    'status' => $request->status,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude
                ]);

                $d_property_specifications = PropertySpecification::where('property_id', $request->property_id)->get();
                foreach ($d_property_specifications as $d_property_specification) {

                    $d_property_specification->deleteSpecification();
                }

                if ($request->has('amenities')) {
                    $property->proertyAmenities()->delete();
                    foreach ($request->amenities as $amenity) {
                        PropertyAmenity::create([
                            'property_id' => $property->id,
                            'amenity_id' => $amenity,
                            'user_id' => $agent->user_id
                        ]);
                    }
                }


                foreach ($languages as $language) {
                    if ($request->filled($language->code . '_title') || $request->filled($language->code . '_address') || $request->filled($language->code . '_description') || $request->filled($language->code . '_meta_keyword') || $request->filled($language->code . '_meta_description')) {
                        $propertyContent =  PropertyContent::where('property_id', $request->property_id)->where('language_id', $language->id)->first();

                        if (empty($propertyContent)) {
                            $propertyContent = new PropertyContent();
                        }

                        $propertyContent->language_id = $language->id;
                        $propertyContent->property_id = $property->id;
                        $propertyContent->title = $request[$language->code . '_title'];
                        $propertyContent->slug = $request[$language->code . '_title'];
                        $propertyContent->address = $request[$language->code . '_address'];
                        $propertyContent->description = $request[$language->code . '_description'];
                        $propertyContent->meta_keyword = $request[$language->code . '_meta_keyword'];
                        $propertyContent->meta_description = $request[$language->code . '_meta_description'];
                        $propertyContent->save();
                    }
                    $label_datas = $request[$language->code . '_label'];
                    foreach ($label_datas as $key => $data) {
                        if (!empty($request[$language->code . '_value'][$key])) {
                            $property_specification = PropertySpecification::where([['property_id', $property->id], ['key', $key]])->first();
                            if (is_null($property_specification)) {
                                $property_specification = new PropertySpecification();
                                $property_specification->property_id = $property->id;
                                $property_specification->user_id = $agent->user_id;
                                $property_specification->key  = $key;
                                $property_specification->save();
                            }
                            $property_specification_content = new PropertySpecificationContent();
                            $property_specification_content->language_id = $language->id;
                            $property_specification_content->property_spacification_id = $property_specification->id;
                            $property_specification_content->label = $data;
                            $property_specification_content->value = $request[$language->code . '_value'][$key];
                            $property_specification_content->save();
                        }
                    }
                }
            }
        );
        Session::flash('success', __('Updated successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    public function messages($username, Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $tanant_id = $agent->user_id;
        if ($request->has('language')) {
            $language = $this->selectLang($tanant_id, $request->language);
        } else {
            $language = $this->defaultLang($tanant_id);
        }

        $title = null;

        if (request()->filled('title')) {
            $title = $request->title;
        }

        $messages = PropertyContact::where('user_property_contacts.agent_id', $agent->id)
            ->leftJoin('user_properties', 'user_property_contacts.property_id', 'user_properties.id')
            ->leftJoin('user_property_contents', 'user_properties.id', 'user_property_contents.property_id')
            ->where('user_property_contents.language_id', $language->id)
            ->when($title, function ($query) use ($title) {
                return $query->where('user_property_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->select('user_property_contacts.*', 'user_property_contents.title', 'user_property_contents.slug')
            ->latest()->get();


        return view('agent.property.message', compact('messages'));
    }
    public function destroyMessage(Request $request, $username)
    {
        $message = PropertyContact::where('agent_id', Auth::guard('agent')->user()->id)->find($request->message_id);
        if ($message) {
            $message->delete();
            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('warning', __('Something went wrong!'));
        }
        return redirect()->back();
    }

    public function getStateCities(Request $request, $username)
    {
        $tenantId = Auth::guard('agent')->user()->user_id;
        $language = $this->defaultLang($tenantId);

        $states = State::where('country_id', $request->id)->with(['cities', 'stateContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $states->map(function ($state) use ($language) {
            $state->name = $state->getContent($language->id)->name;
        });

        $cities = City::where('country_id', $request->id)->where('status', 1)->with(['cityContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $cities->map(function ($city) use ($language) {
            $city->name = $city->getContent($language->id)->name;
        });

        return Response::json(['states' => $states, 'cities' => $cities], 200);
    }
    public function getCities(Request $request, $username)
    {
        $tenantId = Auth::guard('agent')->user()->user_id;
        $language = $this->defaultLang($tenantId);
        $cities = City::where('state_id', $request->state_id)->where('status', 1)->with(['cityContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $cities->map(function ($city) use ($language) {
            $city->name = $city->getContent($language->id)->name;
        });
        return Response::json(['cities' => $cities], 200);
    }

    public function videoImgrmv(Request $request)
    {
        $pi = Property::select('video_image', 'id')->findOrFail($request->fileid);

        if (!empty($pi->video_image)) {
            @unlink(public_path('assets/img/property/video/') . $pi->video_image);
            $pi->video_image = null;
            $pi->save();
            return 'success';
        } else {
            return 'false';
        }
    }

    public function floorImgrmv(Request $request)
    {
        $pi = Property::select('floor_planning_image', 'id')->findOrFail($request->fileid);

        if (!empty($pi->floor_planning_image)) {
            @unlink(public_path('assets/img/property/plannings/') . $pi->floor_planning_image);
            $pi->floor_planning_image = null;
            $pi->save();
            return 'success';
        } else {
            return 'false';
        }
    }
    public function specificationDelete(Request $request, $username)
    {
        try {
            $specification = PropertySpecification::find($request->spacificationId);
            $specification->deleteSpecification();

            return Response::json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return Response::json(['status' => 'error'], 400);
        }
    }

    public function delete($username, Request $request)
    {
        try {
            $agentId = Auth::guard('agent')->user()->id;
            $property = Property::where([['agent_id', $agentId], ['id', $request->property_id]])->first();
            if ($property) {
                $property->destroyPropertry();
            }
        } catch (\Exception $e) {
            Session::flash('warning', __('Something went wrong!'));

            return redirect()->back();
        }

        Session::flash('success', __('Deleted successfully!'));
        return redirect()->back();
    }


    public function bulkDelete($username, Request $request)
    {

        $propertyIds = $request->ids;
        try {
            foreach ($propertyIds as $id) {
                $agentId = Auth::guard('agent')->user()->id;
                $property = Property::where([['agent_id', $agentId], ['id', $id]])->first();
                if ($property) {
                    $property->destroyPropertry();
                }
            }
        } catch (\Exception $e) {
            Session::flash('warning', __('Something went wrong!'));

            return redirect()->back();
        }
        Session::flash('success', __('Deleted successfully!'));
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Move AI-generated image from temp to permanent storage
     */
    private function moveAiImage(string $sourcePath, string $destinationDir, string $prefix): ?string
    {
        $fullSourcePath = public_path($sourcePath);

        if (!File::exists($fullSourcePath)) {
            return null;
        }

        $fileName = $prefix . time() . '_' . uniqid() . '.png';
        $destinationPath = public_path($destinationDir . '/' . $fileName);

        // Ensure directory exists
        $directory = dirname($destinationPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Copy & return new filename
        if (File::copy($fullSourcePath, $destinationPath)) {
            return $fileName;
        }

        return null;
    }

    /**
     * Optional: Clean up temporary AI-generated images
     */
    private function cleanupAiTempImages(Request $request): void
    {
        $tempPaths = [];

        if ($request->filled('ai_thumbnail_path')) {
            $tempPaths[] = $request->ai_thumbnail_path;
        }
        if ($request->filled('ai_floor_plan_path')) {
            $tempPaths[] = $request->ai_floor_plan_path;
        }
        if ($request->filled('ai_video_poster_path')) {
            $tempPaths[] = $request->ai_video_poster_path;
        }
        if ($request->has('ai_slider_images')) {
            $tempPaths = array_merge($tempPaths, $request->ai_slider_images);
        }

        foreach ($tempPaths as $path) {
            $fullPath = public_path($path);
            if (File::exists($fullPath)) {
                File::delete($fullPath); // Uncomment to delete
            }
        }
    }
}
