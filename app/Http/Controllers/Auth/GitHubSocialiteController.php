<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use TCG\Voyager\Models\Role;

class GitHubSocialiteController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback()
    {
        $github = Socialite::driver('github')->user();
        $role   = Role::where('name', '=', config('voyager.user.default_role'))->first();

        $trial_days    = setting('billing.trial_days', 14);
        $trial_ends_at = null;

        if (intval($trial_days) > 0) {
            $trial_ends_at = now()->addDays(setting('billing.trial_days', 14));
        }

        $user = User::query()
            ->where('provider_id', $github->getId())
            ->first();

        if ($user !== null) {
            return $this->login($user);
        }

        $user = auth()->check()
            ? auth()->user()
            : User::query()
                ->where('email', $github->getEmail())
                ->first();

        if (! $user) {
            $user = User::query()->create([
                'provider_id'   => $github->getId(),
                'email'         => $github->getEmail(),
                'name'          => $github->getName(),
                'password'      => bcrypt(str_random()),
                'username'      => $github->getNickname(),
                'verified'      => 1,
                'trial_ends_at' => $trial_ends_at,
                'role'          => $role->id,
            ]);
        }

        $user->update([
            'provider_id' => $github->getId(),
        ]);
        
        return $this->login($user);
    }

    protected function login($user) {
        auth()->guard()->login($user, false);

        return redirect()
            ->route('wave.dashboard')
            ->with([
                'message'      => 'Successfully updated your profile information.',
                'message_type' => 'success',
            ]);
    }
}
