<?php
$totals = [
    ['key' => __('student.totals.in_progress'), 'color' => 'text-info', 'bg' => 'bg-info/10', 'icon' => 'icon-[mdi--school-outline]', 'count' => $totals['in_progress']], 
    ['key' => __('student.totals.completed'), 'color' => 'text-primary', 'bg' => 'bg-primary/10', 'icon' => 'icon-[mdi--checkbox-marked-circle-outline]', 'count' => $totals['completed']], 
    ['key' => __('student.totals.wishlist'), 'color' => 'text-pink-500', 'bg' => 'bg-pink-50', 'icon' => 'icon-[mdi--heart-outline]', 'count' => $totals['saved']], 
    ['key' => __('student.totals.certificates'), 'color' => 'text-violet-500', 'bg' => 'bg-violet-50', 'icon' => 'icon-[mdi--seal]', 'count' => $totals['certificates']]];
?>
<div class="grid grid-cols-2 md:grid-cols-4 justify-between items-center my-5 gap-4">
    @foreach ($totals as $total)
        <div class="p-5 border border-slate-300 rounded-lg flex items-center gap-3">
            <span class="p-2 {{ $total['bg'] }} rounded-2xl flex items-center"><i
                    class="{{ $total['icon'] }} size-8 {{ $total['color'] }}"></i></span>
            <div class="">
                <h3 class="font-bold text-xl">{{ $total['count'] }}</h3>
                <p class="capitalize text-slate-500 text-sm">{{ $total['key'] }}</p>
                <div>
                </div>
            </div>
        </div>
    @endforeach
</div>
