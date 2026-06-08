<?php

namespace App\Http\Controllers;

use App\Classes\GenerateStrongPassword;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Nnjeim\World\World;
use Illuminate\Support\Facades\Mail;


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
}
