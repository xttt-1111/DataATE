<?php

namespace App\Http\Controllers;

use App\Models\Loyalty;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    // My Vouchers Page (Profile)
    public function index()
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return redirect()->route('home');
        }

        $activeVouchers = $customer->vouchers()->active()->latest()->get();
        $pastVouchers = $customer->vouchers()->past()->latest()->get();

        return view('profile.vouchers', compact('activeVouchers', 'pastVouchers'));
    }

    // Redemption Selection Page (Loyalty)
    public function redeemPage()
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return redirect()->route('home');
        }

        $currentLoyalty = $customer->currentLoyalty();
        $stamps = $currentLoyalty ? $currentLoyalty->rental_counter : 0;

        return view('loyalty.redeem', compact('stamps'));
    }

    // Process Redemption
    public function store(Request $request)
    {
        $request->validate([
            'tier' => 'required|integer|in:3,6,9,12,15',
        ]);

        $tierCost = $request->tier;
        $customer = Auth::user()->customer;
        $currentLoyalty = $customer->currentLoyalty();
        $currentStamps = $currentLoyalty ? $currentLoyalty->rental_counter : 0;

        if ($currentStamps < $tierCost) {
            return redirect()->back()->with('error', 'Not enough stamps for this reward.');
        }

        // Determine Reward
        $discount = null;
        $freeHours = null;

        switch ($tierCost) {
            case 3: $discount = 10; break;
            case 6: $discount = 15; break;
            case 9: $discount = 20; break;
            case 12: $discount = 25; break;
            case 15: $freeHours = 12; break;
        }

        // 1. Create Voucher
        Voucher::create([
            'customer_id' => $customer->id,
            'voucher_code' => strtoupper(Str::random(8)),
            'discount_percent' => $discount,
            'free_hours' => $freeHours,
            'expiry_date' => now()->addDays(30), // Valid for 30 days default
            'status' => 'active',
        ]);

        // 2. Deduct Stamps (Logic: Spend stamps, keep remainder)
        $newCounter = $currentStamps - $tierCost;

        Loyalty::create([
            'customer_id' => $customer->id,
            'type' => 'redeemed',
            'description' => "Redeemed {$tierCost} stamps for " . ($discount ? "{$discount}% Off" : "{$freeHours}H Free"),
            'rental_counter' => $newCounter,
            'no_of_stamp' => $currentLoyalty->no_of_stamp, // Lifetime stays same
        ]);

        return redirect()->route('loyalty.index')->with('success', 'Voucher redeemed successfully! Check your Profile > My Vouchers.');
    }
}
