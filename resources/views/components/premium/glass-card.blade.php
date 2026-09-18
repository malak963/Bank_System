
<div class="glass-card relative overflow-hidden rounded-2xl backdrop-blur-xl border border-{{ $borderColor }} shadow-2xl"
     style="background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));">
    
    <!-- Animated gradient background -->
    <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }} opacity-50 animate-pulse"></div>
    
    <!-- Glass shine effect -->
    <div class="absolute inset-0 bg-gradient-to-tr from-white/10 via-transparent to-transparent"></div>
    
    <!-- Content -->
    <div class="relative z-10 p-6">
        @if($title)
            <h3 class="text-lg font-bold text-white mb-4">{{ $title }}</h3>
        @endif
        
        {{ $slot }}
    </div>
    
    <!-- Subtle border glow -->
    <div class="absolute inset-0 rounded-2xl shadow-inner ring-1 ring-inset ring-white/10"></div>
</div>

<style>
    .glass-card {
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    @media (prefers-reduced-motion: reduce) {
        .glass-card {
            transition: none;
            transform: none;
        }
    }
</style>
