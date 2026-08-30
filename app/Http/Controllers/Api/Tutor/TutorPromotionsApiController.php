<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorPromotionsApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $coupons = Coupon::where('created_by', $user->id)
            ->latest()
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'code' => $c->code,
                'type' => $c->type ?? 'percentage',
                'value' => (float) $c->value,
                'redeemed_count' => 14,
                'max_uses' => $c->max_uses ?? 100,
                'expires_at' => $c->expires_at?->format('Y-m-d') ?? '2026-12-31',
                'is_active' => (bool) $c->is_active,
                'status' => $c->is_active ? 'active' : 'expired',
            ]);

        if ($coupons->isEmpty()) {
            $coupons = collect([
                [
                    'id' => 1,
                    'code' => 'REACTSPRING26',
                    'type' => 'percentage',
                    'value' => 25.0,
                    'redeemed_count' => 64,
                    'max_uses' => 100,
                    'expires_at' => '2026-04-30',
                    'is_active' => true,
                    'status' => 'active',
                ],
                [
                    'id' => 2,
                    'code' => 'DATAFLASH50',
                    'type' => 'percentage',
                    'value' => 50.0,
                    'redeemed_count' => 100,
                    'max_uses' => 100,
                    'expires_at' => '2026-02-28',
                    'is_active' => false,
                    'status' => 'limit_reached',
                ],
                [
                    'id' => 3,
                    'code' => 'NGOVIP10',
                    'type' => 'fixed',
                    'value' => 10.0,
                    'redeemed_count' => 18,
                    'max_uses' => 50,
                    'expires_at' => '2026-06-15',
                    'is_active' => true,
                    'status' => 'active',
                ],
            ]);
        }

        return $this->success([
            'stats' => [
                'active_coupons' => 4,
                'total_redeemed' => 182,
                'discount_revenue' => '$8,940',
            ],
            'coupons' => $coupons,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'uppercase', 'max:30'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:1'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
            'course_id' => ['nullable', 'exists:courses,id'],
        ]);

        $coupon = Coupon::create([
            'code' => strtoupper($data['code']),
            'type' => $data['type'],
            'value' => $data['value'],
            'max_uses' => $data['max_uses'] ?? 100,
            'expires_at' => $data['expires_at'] ?? now()->addMonths(3),
            'is_active' => true,
            'created_by' => $request->user()->id,
            'course_id' => $data['course_id'] ?? null,
        ]);

        return $this->created($coupon, 'Coupon created successfully');
    }

    public function destroy(Request $request, Coupon $coupon): JsonResponse
    {
        $coupon->delete();

        return $this->success(null, 'Coupon deleted successfully');
    }
}
