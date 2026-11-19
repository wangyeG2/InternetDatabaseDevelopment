/* 绿茵前线 · 秦东杰 */
(() => {
    if (window.gsap && !matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    gsap.from(entry.target, {y: 8, duration: 0.32, clearProps: 'transform'});
                    observer.unobserve(entry.target);
                }
            });
        });
        document.querySelectorAll('.wc-faq-item, .team-roster .team-row').forEach(item => observer.observe(item));
    }
})();
