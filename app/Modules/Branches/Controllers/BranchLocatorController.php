<?php

namespace App\Modules\Branches\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Branches\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchLocatorController extends Controller
{
    public function nearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:100',
            'services' => 'nullable|array',
            'services.*' => 'in:atm,branch,cashDeposit,loanAdvisor',
        ]);

        $lat = $validated['lat'];
        $lng = $validated['lng'];
        $radius = $validated['radius'] ?? 10; // default 10km

        // Haversine formula for distance calculation
        $branches = Branch::query()
            ->where('status', 'open')
            ->selectRaw("
                id,
                code,
                name,
                address,
                phone,
                email,
                latitude,
                longitude,
                manager_id,
                status,
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
            ", [$lat, $lng, $lat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->get();

        // Add live queue data (simplified for now)
        $branchesWithLiveData = $branches->map(function ($branch) {
            return [
                'id' => $branch->id,
                'type' => 'branch',
                'name' => $branch->name,
                'code' => $branch->code,
                'coordinates' => [
                    'lat' => (float) $branch->latitude,
                    'lng' => (float) $branch->longitude,
                ],
                'address' => $branch->address,
                'phone' => $branch->phone,
                'email' => $branch->email,
                'distance' => round((float) $branch->distance, 2),
                'liveData' => [
                    'queueLength' => 0, // Will be integrated with Queue module
                    'estimatedWaitTime' => 0, // Will be integrated with Queue module
                    'atmAvailability' => 'available',
                    'staffPresence' => $branch->manager_id ? 1 : 0,
                ],
                'services' => ['branch', 'cashDeposit', 'loanAdvisor'],
                'accessibility' => true,
                'lastUpdated' => now()->toIso8601String(),
            ];
        });

        return response()->json([
            'locations' => $branchesWithLiveData,
            'total' => $branchesWithLiveData->count(),
        ]);
    }

    public function realtimeQueue(Branch $branch): JsonResponse
    {
        // Simplified version without Queue module dependency
        return response()->json([
            'branchId' => $branch->id,
            'currentWaitTime' => 0,
            'activeCustomers' => 0,
            'availableSlots' => $this->getAvailableSlots(),
            'branchCapacity' => [
                'current' => 0,
                'max' => 20,
                'trend' => 'stable',
            ],
            'currentlyServing' => null,
            'lastUpdated' => now()->toIso8601String(),
        ]);
    }

    private function getAvailableSlots(): array
    {
        // Generate available time slots for today
        $slots = [];
        $currentTime = now()->setMinute(0)->setSecond(0);
        $closingTime = now()->setHour(17)->setMinute(0)->setSecond(0); // 5 PM closing

        while ($currentTime < $closingTime) {
            $slots[] = [
                'time' => $currentTime->format('H:i'),
                'serviceType' => 'general',
                'estimatedDuration' => 15,
            ];
            $currentTime->addMinutes(30);
        }

        return $slots;
    }
}
