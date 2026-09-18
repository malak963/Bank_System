<?php

namespace App\Modules\Cards\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Models\Account;
use App\Modules\Cards\Requests\CreateCardRequest;
use App\Modules\Cards\Services\CardService;
use App\Modules\Cards\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CardController extends Controller
{
    private CardService $cardService;

    public function __construct(CardService $cardService)
    {
        $this->cardService = $cardService;
    }

    public function index(): View
    {
        $cards = Card::with(['account', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('cards::index', compact('cards'));
    }

    public function create(): View
    {
        $accounts = Account::where('status', 'open')->get();
        return view('cards::create', compact('accounts'));
    }

    public function store(CreateCardRequest $request)
    {
        try {
            $card = $this->cardService->createCard($request->validated());
            
            return redirect()
                ->route('cards.show', $card)
                ->with('success', 'Card created successfully. Please note: PIN = ' . $card->pin);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Card creation failed: ' . $e->getMessage());
        }
    }

    public function show(Card $card): View
    {
        $card->load(['account', 'customer', 'replacementCard', 'replacedCards']);
        return view('cards::show', compact('card'));
    }

    public function activate(Request $request, Card $card)
    {
        $validated = $request->validate([
            'pin' => 'required|string|size:4',
        ]);

        try {
            $card = $this->cardService->activateCard($card, $validated['pin']);
            return redirect()
                ->route('cards.show', $card)
                ->with('success', 'Card activated successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Activation failed: ' . $e->getMessage());
        }
    }

    public function block(Request $request, Card $card)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $card = $this->cardService->blockCard($card, $validated['reason']);
            return redirect()
                ->route('cards.show', $card)
                ->with('success', 'Card blocked successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Block failed: ' . $e->getMessage());
        }
    }

    public function unblock(Card $card)
    {
        try {
            $card = $this->cardService->unblockCard($card);
            return redirect()
                ->route('cards.show', $card)
                ->with('success', 'Card unblocked successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Unblock failed: ' . $e->getMessage());
        }
    }

    public function replace(Request $request, Card $card)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'daily_limit' => 'nullable|numeric|min:0|max:100000',
            'monthly_limit' => 'nullable|numeric|min:0|max:1000000',
            'delivery_method' => 'nullable|string|in:branch,mail,courier',
            'priority' => 'nullable|string|in:standard,express,urgent',
        ]);

        try {
            $newCard = $this->cardService->replaceCard($card, $validated['reason'], $validated);
            return redirect()
                ->route('cards.show', $newCard)
                ->with('success', 'Card replaced successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Replacement failed: ' . $e->getMessage());
        }
    }

    public function updatePin(Request $request, Card $card)
    {
        $validated = $request->validate([
            'current_pin' => 'required|string|size:4',
            'new_pin' => 'required|string|size:4|different:current_pin',
        ]);

        try {
            $card = $this->cardService->updatePIN($card, $validated['current_pin'], $validated['new_pin']);
            return redirect()
                ->route('cards.show', $card)
                ->with('success', 'PIN updated successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'PIN update failed: ' . $e->getMessage());
        }
    }

    public function updateLimits(Request $request, Card $card)
    {
        $validated = $request->validate([
            'daily_limit' => 'nullable|numeric|min:0|max:100000',
            'monthly_limit' => 'nullable|numeric|min:0|max:1000000',
        ]);

        try {
            $card = $this->cardService->updateLimits($card, $validated);
            return redirect()
                ->route('cards.show', $card)
                ->with('success', 'Limits updated successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Limits update failed: ' . $e->getMessage());
        }
    }

    public function toggleFeatures(Request $request, Card $card)
    {
        $validated = $request->validate([
            'international_enabled' => 'nullable|boolean',
            'online_enabled' => 'nullable|boolean',
            'contactless_enabled' => 'nullable|boolean',
        ]);

        try {
            $card = $this->cardService->toggleFeatures($card, $validated);
            return redirect()
                ->route('cards.show', $card)
                ->with('success', 'Features updated successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Features update failed: ' . $e->getMessage());
        }
    }

    // API Methods
    public function apiIndex(Request $request): JsonResponse
    {
        $query = Card::with(['account', 'customer']);

        if ($request->has('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('card_type', $request->type);
        }

        $cards = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json($cards);
    }

    public function apiStore(CreateCardRequest $request): JsonResponse
    {
        try {
            $card = $this->cardService->createCard($request->validated());
            return response()->json($card, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiShow(Card $card): JsonResponse
    {
        $card->load(['account', 'customer', 'replacementCard', 'replacedCards']);
        return response()->json($card);
    }

    public function apiByAccount(Account $account): JsonResponse
    {
        $cards = $this->cardService->getCardsByAccount($account);
        return response()->json($cards);
    }

    public function apiActivate(Request $request, Card $card): JsonResponse
    {
        $validated = $request->validate([
            'pin' => 'required|string|size:4',
        ]);

        try {
            $card = $this->cardService->activateCard($card, $validated['pin']);
            return response()->json($card, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiBlock(Request $request, Card $card): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $card = $this->cardService->blockCard($card, $validated['reason']);
            return response()->json($card, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
