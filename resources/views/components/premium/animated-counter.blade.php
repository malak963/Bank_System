<div x-data="animatedCounter({{ $target }}, {{ $duration }}, {{ $decimals }})"
     x-init="startAnimation()"
     class="animated-counter">
    <span x-text="displayValue">{{ $prefix }}0{{ $suffix }}</span>
</div>

@push('scripts')
<script>
    function animatedCounter(target, duration, decimals) {
        return {
            target: target,
            duration: duration,
            decimals: decimals,
            currentValue: 0,
            displayValue: '0',
            startTime: null,
            
            startAnimation() {
                // Check for reduced motion preference
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    this.displayValue = this.formatNumber(this.target);
                    return;
                }
                
                this.startTime = performance.now();
                this.animate();
            },
            
            animate(currentTime) {
                if (!this.startTime) this.startTime = currentTime;
                
                const elapsed = currentTime - this.startTime;
                const progress = Math.min(elapsed / this.duration, 1);
                
                // Easing function (ease-out)
                const easeOut = 1 - Math.pow(1 - progress, 3);
                
                this.currentValue = this.target * easeOut;
                this.displayValue = this.formatNumber(this.currentValue);
                
                if (progress < 1) {
                    requestAnimationFrame(this.animate.bind(this));
                }
            },
            
            formatNumber(value) {
                const formatted = value.toFixed(this.decimals);
                return `{{ $prefix }}${formatted}{{ $suffix }}`;
            }
        }
    }
</script>
@endpush
