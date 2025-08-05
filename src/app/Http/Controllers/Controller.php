<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // ← Laravelの元Controller
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Pair;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected $authUser;
    protected $pair;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authUser = Auth::user();

            if ($this->authUser) {
                View::share('authUserName', $this->authUser->name);

                $this->pair = Pair::where(function ($query) {
                    $query->where('user1_id', $this->authUser->id)
                        ->orWhere('user2_id', $this->authUser->id);
                })->where('status', 'accepted')->first();

                View::share('pair', $this->pair);

                // ✅ 家族メンバーも全画面で共有可能にする
                $members = $this->getMembers();
                View::share('members', $members);
            }

            return $next($request);
        });
    }

    protected function getAuthUser()
    {
        return $this->authUser;
    }

    protected function getMembers()
    {
        return Auth::user()
            ->family
            ->users()
            ->where('id', '!=', Auth::id())
            ->get();
    }
}
