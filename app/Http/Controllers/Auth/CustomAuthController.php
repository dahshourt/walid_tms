<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Repository\Groups\GroupRepository;
use App\Http\Repository\Roles\RolesRepository;
use App\Http\Repository\Users\UserRepository;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Log;
use Redirect;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function CheckLdapAccount($user_name, $password)
    {
        $ldapconn = @ldap_connect(config('constants.cairo.ldap_host'));
        $response = [];
        if (! $ldapconn) {
            $response['message'] = 'There is a connection problem with ldap.';
            $response['status'] = false;

            return $response;
        }
        $ldap_binddn = config('constants.cairo.ldap_binddn') . $user_name;

        $ldapbind = @ldap_bind($ldapconn, $ldap_binddn, $password);
        if (! $ldapbind) {
            $response['message'] = 'Credentials Invalid.';
            $response['status'] = false;

            return $response;
            // return \Redirect::back()->withErrors(['msg' => "Credentials Invalid."])->withInput();
        }
        $response['message'] = 'Success';
        $response['status'] = true;

        return $response;
    }

    /**
     * Login
     *
     * @param  [string] username
     * @param  [string] password
     * @return [object] user data
     *
     * @throws \SMartins\PassportMultiauth\Exceptions\MissingConfigException
     */
    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'password' => 'required',
        ]);

        $generalLoginError = 'Login error. Please contact administration.';
        $accountLockedError = 'Your account is locked due to too many failed login attempts. Please contact your administrator.';

        $maxAttempts = config('auth.max_login_attempts', 5); // default to 5 if not set

        $user = User::with('user_groups', 'user_groups.group', 'defualt_group')
            ->where('user_name', $request->user_name)
            ->first();

        // User not found in local DB, try LDAP
        if (! $user) {
            $response = $this->CheckLdapAccount($request->user_name, $request->password);
            if ($response['status']) {
                $email = $request->user_name . '@te.eg';
                $check_email = (new UserRepository)->CheckUniqueEmail($email);

                $roles = [];
                $group_id = [];
                $role = (new RolesRepository)->findByName('Viewer');
                $bussines_group = (new GroupRepository)->findByName('Business Team');
                $roles[] = $role->name;
                $default_group = $bussines_group->id;
                $group_id[] = $bussines_group->id;

                if ($check_email) {
                    return Redirect::back()->withErrors(['msg' => $generalLoginError])->withInput();
                }

                $data = [
                    'user_type' => 1,
                    'name' => $request->user_name,
                    'user_name' => $request->user_name,
                    'email' => $email,
                    'roles' => $roles,
                    'default_group' => $default_group,
                    'group_id' => $group_id,
                    'active' => '1',
                ];

                $user = (new UserRepository)->create($data);
                Auth::login($user);
                DB::table('sessions')->where('user_id', $user->id)->where('id', '!=', Session::getId())->delete();

                return redirect()->intended(url('/'));
            }

            return Redirect::back()->withErrors(['msg' => $generalLoginError])->withInput();

        }

        // Check if account is locked
        if ($user->failed_attempts >= $maxAttempts) {
            $user->active = 0;
            $user->save();
        }

        if ($user->active == 0) {
            return redirect('login')->with('failed', $accountLockedError);
        }

        // All users are now LDAP/AD users
        $response = $this->CheckLdapAccount($request->user_name, $request->password);
        if ($response['status']) {
            $user->failed_attempts = 0;
            $user->save();

            Auth::login($user);
            DB::table('sessions')->where('user_id', $user->id)->where('id', '!=', Session::getId())->delete();

            return redirect()->intended(url('/'));
        }
        $user->failed_attempts += 1;
        if ($user->failed_attempts >= $maxAttempts) {
            $user->active = 0;
        }
        $user->save();

        return Redirect::back()->withErrors(['msg' => $generalLoginError])->withInput();

    }

    /**
     * Logout user (Revoke the token)
     *
     * @param  [string] token
     * @return [array] msg
     */
    public function logout(Request $request)
    {
        try {
            $user = User::where('id', $request->user()->id)->first();
            $user->device_token = null;
            $user->save();
            $request->user()->tokens()->delete();

            return response()->json(['msg' => [__('messages.logout_successfully')]], 200);
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return response()->json(['msg' => [__('messages.failed_request')]], 403);
        }
    }

    /**
     * Reset Password
     *
     * @param  [string] password
     * @param  [string] password confiramtion
     * @return [array] msg
     *
     * @throws \SMartins\PassportMultiauth\Exceptions\MissingConfigException
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = User::where('phone', $request->phone)->first();
            // reset user password
            $user->password = Hash::make($request->password);
            $user->save();

            // Revoke a all user tokens...
            return response()->json(['msg' => [__('messages.reset_successfully')]], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug($e->getMessage());

            return response()->json(['msg' => [__('messages.failed_request')]], 403);
        }
    }

    /**
     * Update Password
     *
     * @param  [string] old_password
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [array] msg
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {

        try {
            DB::beginTransaction();
            $user = User::where('id', $request->user()->id)->first();
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->password);
                $user->save();
            } else {
                return response()->json(['msg' => [__('messages.old_password_is_incorrect')]], 403);
            }
            DB::commit();

            return response()->json(['msg' => [__('messages.success_update')]], 200);
        } catch (Exception $e) {
            DB::rollback();
            Log::debug($e->getMessage());

            return response()->json(['msg' => [__('messages.failed_request')]], 403);
        }
    }

    public function check_active()
    {
        // dd(auth()->user(),Auth::check());
        return response()->json([
            'active' => Auth::check() ? Auth::user()->active : false,
        ]);
    }

    public function inactive_logout()
    {

        Auth::logout();

        return redirect('/login')->withErrors(['msg' => 'Login error. Please contact administration.'])->withInput();

    }
}
