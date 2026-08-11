<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    /**
     * Index: show login history grouped per user account with total login count.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');

        // Build query: group by user_id, count total logins, last login time
        $usersQuery = User::withCount(['loginHistories as total_logins' => function ($q) use ($date) {
                $q->when($date, fn($q) => $q->whereDate('login_at', $date));
            }])
            ->withMax(['loginHistories as last_login' => function ($q) use ($date) {
                $q->when($date, fn($q) => $q->whereDate('login_at', $date));
            }], 'login_at')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->having('total_logins', '>', 0)
            ->orderByDesc('last_login');

        $users = $usersQuery->paginate(25);

        return view('login_histories.index', compact('users', 'search', 'date'));
    }

    /**
     * Show: detail login history for a specific user.
     */
    public function showUser(Request $request, User $user)
    {
        $date = $request->input('date');

        $histories = LoginHistory::where('user_id', $user->id)
            ->when($date, function ($query, $date) {
                return $query->whereDate('login_at', $date);
            })
            ->orderBy('login_at', 'desc')
            ->paginate(25);

        return view('login_histories.show_user', compact('user', 'histories', 'date'));
    }
}
