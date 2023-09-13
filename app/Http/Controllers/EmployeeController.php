<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            [
                'name' => $name,
                'username' => $username,
                'password' => $password,
                'gender' => $gender,
                'phone' => $phone,
                'groups' => $groups
            ] = $request->validate([
                'name' => "required|string",
                'username' => "required|string",
                'password' => "required|string",
                'gender' => [
                    "required",
                    Rule::in("male", "female")
                ],
                'phone' => "required|string|numeric",
                'groups' => "array",
                'groups.*.id' => "numeric",
                'groups.*.name' => "required_without:groups.*.id|string"
            ]) + ['groups' => []];

            /**
             * @var $user User
             */
            $user = User::create([
                'name' => $name,
                'username' => $username,
                'password' => $password,
                'role' => "employee"
            ]);

            $profile = new Profile();
            $profile->gender = $gender;
            $profile->phone = $phone;
            $profile->user()->associate($user);

            $profile->save();

            $createGroups = collect($groups)
                ->filter(fn($item) => !((int)isset($item['id']) ? $item['id'] : 0))
                ->map(fn($item) => ['name' => $item['name']])
                ->toArray();
            $selectedGroups = collect($groups)
                ->filter(fn($item) => ((int)isset($item['id']) ? $item['id'] : 0))
                ->map(fn($item) => (int)$item['id'])
                ->toArray();

            if (count($createGroups)) {
                foreach ($createGroups as $group) {
                    $create = Group::create($group);
                    $selectedGroups[] = $create->id;
                }
            }
            if (count($selectedGroups)) {
                $user->groups()->sync($selectedGroups);
            }

            return $user->toArray();
        });
    }

    public function index()
    {
        // Ambil semua data employee
        // Paginasi dan sort di client-side karena tidak dijelaskan harus server atau client yang handle
        $employees = User::query()->where("role", "employee")->with("profile")->with("groups")->get();

        return $employees->toArray();
    }

    public function groups()
    {
        return Group::all()->toArray();
    }

    public function update(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        return DB::transaction(function () use ($request, $employee) {

            [
                'name' => $name,
                'username' => $username,
                'password' => $password,
                'gender' => $gender,
                'phone' => $phone,
                'groups' => $groups
            ] = $request->validate([
                'name' => "required|string",
                'username' => "required|string",
                'password' => "nullable|string",
                'gender' => [
                    "required",
                    Rule::in("male", "female")
                ],
                'phone' => "required|string|numeric",
                'groups' => "array",
                'groups.*.id' => "numeric",
                'groups.*.name' => "required_without:groups.*.id|string"
            ]) + ['password' => null, 'groups' => []];

            $employee->name = $name;
            $employee->username = $username;

            if ($password) {
                $employee->password = $password;
            }

            $employee->save();

            $employee->profile->gender = $gender;
            $employee->profile->phone = $phone;
            $employee->profile->save();

            $createGroups = collect($groups)
                ->filter(fn($item) => !((int)isset($item['id']) ? $item['id'] : 0))
                ->map(fn($item) => ['name' => $item['name']])
                ->toArray();
            $selectedGroups = collect($groups)
                ->filter(fn($item) => ((int)isset($item['id']) ? $item['id'] : 0))
                ->map(fn($item) => (int)$item['id'])
                ->toArray();

            if (count($createGroups)) {
                foreach ($createGroups as $group) {
                    $create = Group::create($group);
                    $selectedGroups[] = $create->id;
                }
            }
            if (count($selectedGroups)) {
                $employee->groups()->sync($selectedGroups);
            }

            return $employee->toArray();
        });
    }

    public function destroy(Request $request, $id)
    {
        /**
         * @var $employee User
         */
        $employee = User::findOrFail($id);
        $employee->delete();

        return $employee->toArray();
    }
}
