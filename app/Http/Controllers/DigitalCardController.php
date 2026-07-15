<?php

namespace App\Http\Controllers;

use App\Helpers\QrCodeHelper;
use App\Helpers\VcfHelper;
use App\Models\User;
use App\Models\DigitalCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DigitalCardController extends Controller
{
    public function edit(Request $request)
    {
        $user = auth()->user();
        $digitalCard = $user->digitalCard;
        $step = $request->get('step', 1);

        // Validate step range
        if ($step < 1 || $step > 3) {
            $step = 1;
        }

        if (!$digitalCard) {
            $fullname = explode(' ', $user->name);

            $contactInformation = [];
            $contactInformation['email'] = $user->email;
            $contactInformation['mobile'] = $user->phone;
            $contactInformation['website'] = $user->social_link;

            $digitalCard = new DigitalCard();
            $digitalCard->user_id = $user->id;
            $digitalCard->dcard_id = 'DC-' . strtoupper(Str::random(8));
            $digitalCard->first_name = $fullname[0];
            $digitalCard->last_name = count($fullname) > 1 ? $fullname[1] : null;
            $digitalCard->address = $user->address_line_1;
            $digitalCard->is_active = true;
            $digitalCard->contact_informations = $contactInformation;
            $digitalCard->social_links = [];
            $digitalCard->promotional_content = [];
            $digitalCard->testimonials = [];
            $digitalCard->save();
        }

        return view('student.edit_dcard', compact('user', 'digitalCard', 'step'));
    }

    public function update(Request $request, User $user)
    {
        $digitalCard = $user->digitalCard;

        if (!$digitalCard) {
            $digitalCard = new DigitalCard();
            $digitalCard->user_id = $user->id;
            $digitalCard->dcard_id = 'DC-' . strtoupper(Str::random(8));
        }

        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'brand_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:min_width=195,min_height=72,max_width=195,max_height=72',
            'promotional_title' => 'nullable|string|max:255',
            'promotional_link' => 'nullable|url',
            'promotional_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact_informations' => 'nullable|array',
            'testimonials' => 'nullable|array',
            'social_links' => 'nullable|array',
            'cxm_link' => 'nullable|string',
            'theme_setting' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($digitalCard->profile_image) {
                Storage::delete('digital_cards/profile_images/' . $digitalCard->profile_image);
            }

            $image = $request->file('profile_image');
            $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
            $image->storeAs('digital_cards/profile_images', $imageName, 'public');
            $digitalCard->profile_image = $imageName;
        }

        if ($request->hasFile('brand_banner')) {
            // Delete old banner if exists
            if ($digitalCard->brand_banner) {
                Storage::delete('digital_cards/brand_banners/' . $digitalCard->brand_banner);
            }

            $image = $request->file('brand_banner');
            $imageName = time() . '_banner_' . $user->id . '.' . $image->getClientOriginalExtension();
            $image->storeAs('digital_cards/brand_banners', $imageName, 'public');
            $digitalCard->brand_banner = $imageName;
        }

        // Handle promotional image upload
        $promotionalContent = $digitalCard->promotional_content ?: [];

        if ($request->has('promotional_title')) {
            $promotionalContent['title'] = $request->promotional_title;
        }

        if ($request->has('promotional_link')) {
            $promotionalContent['link'] = $request->promotional_link;
        }

        if ($request->hasFile('promotional_image')) {
            // Delete old image if exists
            if (!empty($promotionalContent['promotional_image_uploaded'])) {
                Storage::delete('digital_cards/promotional_images/' . $promotionalContent['promotional_image_uploaded']);
            }

            $image = $request->file('promotional_image');
            $imageName = time() . '_promo_' . $user->id . '.' . $image->getClientOriginalExtension();
            $image->storeAs('digital_cards/promotional_images', $imageName, 'public');
            $promotionalContent['promotional_image_uploaded'] = $imageName;
        }

        $digitalCard->promotional_content = $promotionalContent;

        // Set other fields
        $digitalCard->first_name = $request->first_name;
        $digitalCard->last_name = $request->last_name;
        $digitalCard->job_title = $request->job_title;
        $digitalCard->company_name = $request->company_name;
        $digitalCard->address = $request->address;
        $digitalCard->contact_informations = $request->contact_informations ?: [];
        $digitalCard->testimonials = $request->testimonials ?: [];
        $digitalCard->social_links = $request->social_links ?: [];
        $digitalCard->cxm_link = $request->cxm_link;
        $digitalCard->theme_setting = $request->theme_setting;
        // $digitalCard->is_active = $request->has('is_active');

        $digitalCard->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Digital card saved successfully!',
                'digital_card' => [
                    // Include the URLs for the images
                    'profile_image_url' => $digitalCard->profile_image_url,
                    'brand_banner_url' => $digitalCard->brand_banner_url,
                    'promotional_image_url' => $digitalCard->promotional_image_url,
                    // Include other fields if needed
                    'first_name' => $digitalCard->first_name,
                    'last_name' => $digitalCard->last_name,
                ]
            ]);
        }

        return redirect()->route('student.edit_dcard', $user)
            ->with('success', 'Digital card updated successfully!');
    }

    public function preview(User $user)
    {
        $digitalCard = $user->digitalCard;

        if (!$digitalCard) {
            abort(404, 'Digital card not found');
        }

        $viewMode = 'preview';

        return view('student.preview_dcard', compact('user', 'digitalCard', 'viewMode'));
    }

    public function digitalCardShow(Request $request)
    {
        $digital_card_id = base64_decode($request->id);

        $digitalCard = DigitalCard::find($digital_card_id);

        if (!$digitalCard) {
            abort(404, 'Digital card not found');
        }

        $user = User::find($digitalCard->user_id);

        if (!$user) {
            abort(404, 'User not found');
        }

        $viewMode = 'normal';

        return view('student.preview_dcard', compact('user', 'digitalCard', 'viewMode'));
    }

    public function downloadVcf(User $user)
    {
        $card = $user->digitalCard;

        $phone = $user->phone ?? '';
        $email = $user->email ?? '';

        $contact = [
            'name' => $user->name,
            'company' => $card->company_name,
            'phone' => $phone,
            'email' => $email
        ];
        $vcf = (new VcfHelper())->generateVcf($contact);

        return response($vcf)
            ->header('Content-Type', 'text/vcard')
            ->header(
                'Content-Disposition',
                'attachment; filename="' . $user->name . '.vcf"'
            );
    }

    public function getQrCode(User $user)
    {
        $digitalCard = $user->digitalCard;

        $url = route(
            'digital-card.show',
            ['id' => base64_encode($digitalCard->id)]
        );

        $filename = 'dcard_' . $digitalCard->id;
        $path = 'digital_cards/qrcodes/' . $filename . '.png';

        if (!Storage::disk('public')->exists($path)) {
            $qrUrl = QrCodeHelper::generate(
                $url,
                $filename,
                'digital_cards/qrcodes'
            );
        } else {

            $qrUrl = asset('storage/' . $path); //Storage::url($path);
        }

        return response()->json([
            'success' => true,
            'qr_code' => $qrUrl
        ]);
    }
}
