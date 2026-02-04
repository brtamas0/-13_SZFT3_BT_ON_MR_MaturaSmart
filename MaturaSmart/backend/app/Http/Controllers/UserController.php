<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function me(Request $request)
    {
        return $request->user();
    }

    public function getNotifications(Request $request)
    {
        // Lekérjük az user összes olvasatlan értesítését
        $notifications = $request->user()->unreadNotifications->filter(function ($notification) {
            $expiresAt = $notification->data['expires_at'] ?? null;

            // Ha van lejárat, és a mostani idő nagyobb mint a lejárat, akkor elrejtjük (false)
            if ($expiresAt && now()->gt($expiresAt)) {
                return false;
            }
            // Egyébként megjelenítjük
            return true;
        });
        return $notifications->values();
    }

    //  Egy konkrét értesítés megjelölése olvasottként.
    public function markNotificationAsRead($id, Request $request)
    {
        // Megkeressük az értesítést az ID alapján a user értesítései között
        $notification = $request->user()
                                ->notifications()
                                ->where('id', $id)
                                ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['status' => 'success']);
    }
}