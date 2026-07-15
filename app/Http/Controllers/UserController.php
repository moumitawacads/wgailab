<?php

namespace App\Http\Controllers;

use App\Classes\GenerateStrongPassword;
use App\Models\DigitalCard;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Nnjeim\World\World;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class UserController extends Controller
{

    public function list(Request $request)
    {
        $user = auth()->user();

        if ($user->role == 'superadmin') {
            $query = User::whereIn('role', ['se', 'instructor', 'admin', 'workforce_development']);
        } else {
            $query = User::where('is_admin', 0)
                ->whereIn('role', ['se', 'instructor']);
        }

        // search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($innerQuery) use ($request) {
                $innerQuery->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // role filter
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }

        // status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', (int)$request->status);
        }

        // Date From filter
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // Date To filter
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $users = $query
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.list', compact('users'));
    }

    public function add()
    {
        $countries = World::countries();
        $title = "Add New ";
        return view('admin.users.form', compact('title', 'countries'));
    }

    public function destroy($id, Request $request)
    {
        $user = User::findOrFail($id);
        $data = [
            'status'       => $request->status,
        ];
        $user->update($data);
        return redirect()->back()->with('success', 'User deactivated successfully!');
    }

    public function edit(Request $request, $id)
    {
        $countries = World::countries();
        $user = User::findOrFail($id);
        $title = "Edit";
        return view('admin.users.form', compact('user', 'title', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id), // 👈 important
            ],
            'phone' => [
                'nullable',
                Rule::unique('users')->ignore($id),
            ],
            'role' => 'required|string',
            'address_line_1' => 'required|string',
            'address_line_2' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'social_link' => 'required|string|url',
        ]);

        $oldEmail = $user->email;
        if ($oldEmail != $request->email) {
            if ($user->role) {
                $plainPassword = (new GenerateStrongPassword())->run();
                $templateEmail = "admin.emails.se_user_credentials";
            } else {
                $plainPassword = 'urz@2026'; // default password
                $templateEmail = "admin.emails.user_credentials";
            }

            Mail::send($templateEmail, [
                'user' => $user,
                'password' => $plainPassword
            ], function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Street Entrepreneurs 3.0 App');
            });
        }

        $data = [
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'role'              => $request->role,
            'address_line_1'    => $request->address_line_1,
            'address_line_2'    => $request->address_line_2,
            'city'              => $request->city,
            'state'             => $request->state,
            'country'           => $request->country,
            'social_link'       => $request->social_link,
        ];
        $user->update($data);
        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'unique:users,phone',
            ],
            'role' => 'required|string',
            'address_line_1' => 'required|string',
            'address_line_2' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'social_link' => 'required|string|url',
        ]);

        if ($request->role) {
            $plainPassword = (new GenerateStrongPassword())->run();
            $templateEmail = "admin.emails.se_user_credentials";
        } else {
            $plainPassword = 'urz@2026'; // default password
            $templateEmail = "admin.emails.user_credentials";
        }
        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'role'        => $request->role,
            'is_admin'    => 0,
            'password'    => Hash::make($plainPassword),
            'og_password'    => $plainPassword,
            'address_line_1'    => $request->address_line_1,
            'address_line_2'    => $request->address_line_2,
            'city'              => $request->city,
            'state'             => $request->state,
            'country'           => $request->country,
            'social_link'       => $request->social_link,
        ];
        $user = User::create($data);
        Mail::send($templateEmail, [
            'user' => $user,
            'password' => $plainPassword
        ], function ($message) use ($user) {

            $message->to($user->email)
                ->subject('Street Entrepreneurs 3.0 App');
        });
        return redirect()->route('admin.users')->with('success', 'User created successfully!');
    }

    public function getStates($country)
    {
        //$country=10;

        $states = World::states([
            'filters' => [
                'country_id' => $country
            ]
        ]);

        return response()->json($states->data);
    }

    public function getCities($state)
    {
        $cities = World::cities([
            'filters' => [
                'state_id' => $state
            ]
        ]);

        return response()->json($cities->data);
    }

    public function togglePublish($userId, Request $request)
    {
        $user = User::findOrFail($userId);

        $digitalCard = DigitalCard::firstOrNew(['user_id' => $userId]);
        $user->digital_card_enabled = $request->digital_card_enabled;
        $user->digital_card_published_at = now();
        $user->save();

        $fullname = explode(' ', $user->name);

        $contactInformation = [];
        $contactInformation['email'] = $user->email;
        $contactInformation['mobile'] = $user->phone;
        $contactInformation['website'] = $user->social_link;

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

        $title = '';
        $dateFormatted = Carbon::now()->format('jS F, Y');
        $timeFormatted = Carbon::now()->format('g:i A');
        if ($request->digital_card_enabled) {
            $message = "Your Digital Card has been published on {$dateFormatted} at {$timeFormatted}. You can now make your own digital card and share it with others.";
            $title = 'New Digital Card Published';
        } else {
            $message = "Your Digital Card has been unpublished by administrator on {$dateFormatted} at {$timeFormatted}.";
            $title = 'Digital Card Unpublished';
        }

        Notification::create([
            'title' => $title,
            'message' => $message,
            'user_id' => $userId,
            'is_read' => 0,
        ]);

        $status = $request->digital_card_enabled ? 'published' : 'unpublished';
        return redirect()->back()->with('success', "Digital Card has been {$status} for {$user->name}");
    }

    /**
     * Admin: Toggle card active status
     */
    public function toggleStatus($id)
    {
        $digitalCard = DigitalCard::findOrFail($id);
        $digitalCard->is_active = !$digitalCard->is_active;
        $digitalCard->save();

        return redirect()->back()->with('success', 'Digital Card status updated successfully');
    }
}
