<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GoogleWalletService;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleWalletController extends Controller
{
    protected $walletService;

    public function __construct()
    {
        try {
            $this->walletService = new GoogleWalletService();
        } catch (Exception $e) {
            Log::error('GoogleWalletService initialization failed: ' . $e->getMessage());
        }
    }

    public function addToWallet(User $user)
    {
        $digitalCard = $user->digitalCard;

        if (!$digitalCard) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Digital card not found'], 404);
            }
            return redirect()->back()->with('error', 'Digital card not found');
        }

        try {
            $walletUrl = $this->walletService->getAddToWalletUrl($digitalCard, $user);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'wallet_url' => $walletUrl
                ]);
            }

            return redirect()->away($walletUrl);
        } catch (Exception $e) {
            Log::error('Google Wallet error: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate wallet pass: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to generate wallet pass: ' . $e->getMessage());
        }
    }
}
