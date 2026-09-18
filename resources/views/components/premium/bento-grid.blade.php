@php
    $gridClasses = 'grid gap-4 p-4';
    $gridLayout = match(count($items)) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
    };
@endphp

<div class="bento-grid {{ $gridClasses }} {{ $gridLayout }}" 
     x-data="bentoGrid({{ $parallax }})"
     x-init="initGrid()">
    
    @foreach($items as $item)
        @php
            $sizeClasses = match($item['size'] ?? 'medium') {
                'small' => 'col-span-1 row-span-1',
                'medium' => 'col-span-1 row-span-1 md:col-span-2 md:row-span-1',
                'large' => 'col-span-1 row-span-2 md:col-span-2 md:row-span-2',
                'wide' => 'col-span-1 row-span-1 md:col-span-3 md:row-span-1',
                default => 'col-span-1 row-span-1',
            };
        @endphp
        
        <div class="bento-card {{ $sizeClasses }} relative overflow-hidden rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl transition-all duration-500 ease-out"
             x-data="bentoCard({{ $item['depth'] ?? 1 }})"
             @mousemove="handleMouseMove($event)"
             @mouseleave="handleMouseLeave()"
             :style="cardStyle">
            
            <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
            
            <div class="relative z-10 p-6 h-full flex flex-col justify-between">
                @if(isset($item['icon']))
                    <div class="mb-4">
                        <span class="text-4xl">{{ $item['icon'] }}</span>
                    </div>
                @endif
                
                <div>
                    @if(isset($item['title']))
                        <h3 class="text-xl font-bold text-white mb-2">{{ $item['title'] }}</h3>
                    @endif
                    
                    @if(isset($item['value']))
                        <p class="text-3xl font-bold text-emerald-400">{{ $item['value'] }}</p>
                    @endif
                    
                    @if(isset($item['description']))
                        <p class="text-sm text-white/70 mt-2">{{ $item['description'] }}</p>
                    @endif
                    
                    @if(isset($item['action']))
                        <button class="mt-4 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors duration-300">
                            {{ $item['action'] }}
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Glassmorphic shine effect -->
            <div class="absolute inset-0 opacity-0 transition-opacity duration-500 pointer-events-none"
                 :class="{ 'opacity-100': isHovered }"
                 :style="shineStyle">
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
    function bentoGrid(parallaxEnabled) {
        return {
            parallaxEnabled: parallaxEnabled,
            initGrid() {
                if (!this.parallaxEnabled) return;
                
                // Reduce motion for users who prefer it
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    this.parallaxEnabled = false;
                }
            }
        }
    }
    
    function bentoCard(depth = 1) {
        return {
            depth: depth,
            mouseX: 0,
            mouseY: 0,
            isHovered: false,
            
            get cardStyle() {
                if (!this.isHovered) return {};
                
                const intensity = 0.15 * this.depth;
                const rotateX = -this.mouseY * intensity * 10;
                const rotateY = this.mouseX * intensity * 10;
                const scale = 1 + Math.abs(this.mouseX) * 0.02;
                
                return {
                    transform: `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(${scale})`,
                    transition: 'transform 0.1s ease-out'
                };
            },
            
            get shineStyle() {
                if (!this.isHovered) return {};
                
                const x = (this.mouseX + 0.5) * 100;
                const y = (this.mouseY + 0.5) * 100;
                
                return {
                    background: `radial-gradient(circle at ${x}% ${y}%, rgba(255,255,255,0.3) 0%, transparent 50%)`,
                };
            },
            
            handleMouseMove(event) {
                const rect = event.currentTarget.getBoundingClientRect();
                this.mouseX = (event.clientX - rect.left) / rect.width - 0.5;
                this.mouseY = (event.clientY - rect.top) / rect.height - 0.5;
                this.isHovered = true;
            },
            
            handleMouseLeave() {
                this.mouseX = 0;
                this.mouseY = 0;
                this.isHovered = false;
            }
        }
    }
</script>
@endpush

<style>
    .bento-grid {
        perspective: 1000px;
    }
    
    .bento-card {
        transform-style: preserve-3d;
        will-change: transform;
    }
    
    @media (prefers-reduced-motion: reduce) {
        .bento-card {
            transition: none !important;
            transform: none !important;
        }
    }
</style>
