<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'coupon_code' => 'nullable|exists:coupons,code',
            'payment_method' => 'required|in:card,cash,online',
        ]);

        $service = Service::findOrFail($request->service_id);
        $user = auth('jwt')->user();

        if ($user->memberships()->where('status', 'active')->where('end_date', '>=', now())->exists()) {
            return response()->json(['error' => 'У вас уже есть активная подписка'], 400);
        }

        $amount = $service->price_cents;

        if ($coupon_code = $request->coupon_code) {
            $coupon = Coupon::where('code', $coupon_code)->first();
            if ($coupon && $coupon->isValid()) {
                $amount = (int) ($amount * (100 - $coupon->discount_percent) / 100);
                $coupon->increment('used_count');
            }
        }

        $payment = Payment::create([
            'user_id' => $user->id,
            'amount_cents' => $amount,
            'currency' => 'RUB',
            'provider' => $request->payment_method,
            'status' => 'paid',
        ]);

        $membership = Membership::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'start_date' => now(),
            'end_date' => now()->addDays($service->duration_days),
            'remaining_visits' => $service->visits_limit,
            'status' => 'active',
        ]);

        $payment->update(['membership_id' => $membership->id]);

        return response()->json([
            'message' => 'Тариф активирован!',
            'membership' => $membership->load('service'),
        ]);
    }
}
