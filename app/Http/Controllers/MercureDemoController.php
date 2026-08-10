<?php

namespace App\Http\Controllers;

use App\Events\DemoPrivateMessageSent;
use App\Events\DemoTick;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MercureDemoController extends Controller
{
    public function index()
    {
        return view('mercure-demo', [
            'users' => User::orderBy('id')->get(),
        ]);
    }

    public function loginAs(User $user)
    {
        // Demo scaffolding only: a password-less GET login must never reach
        // a deployed environment.
        abort_unless(app()->environment('local'), 404);

        Auth::login($user);

        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    public function tick()
    {
        DemoTick::dispatch(now()->toDateTimeString());

        return response()->noContent();
    }

    public function sendPrivateMessage(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => ['required', 'integer'],
            'text' => ['required', 'string', 'max:255'],
        ]);

        DemoPrivateMessageSent::dispatch(
            $request->user(),
            $validated['recipient_id'],
            $validated['text'],
        );

        return response()->noContent();
    }
}
