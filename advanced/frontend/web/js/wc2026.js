/* 绿茵前线 · 秦东杰 */
(() => {
    const clock = document.querySelector('[data-wc-clock]');
    if (clock) {
        const days = Math.ceil((Date.parse('2026-06-11T00:00:00-06:00') - Date.now()) / 86400000);
        clock.textContent = days > 0 ? '距揭幕 ' + days + ' 天' :
            Date.now() < Date.parse('2026-07-20T00:00:00-04:00') ? '赛事进行中' : '赛事已收官 · 2026 典藏版';
    }
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
    const front = document.querySelector('[data-wc-feed]');
    if (!front) return;
    const rail = front.querySelector('[data-wc-news]');
    async function wcLoadNews() {
        try {
            const response = await fetch(front.dataset.wcFeed, {headers:{Accept:'application/json'}});
            if (!response.ok) throw new Error();
            const rows = await response.json();
            if (!rows.length) {
                rail.textContent = '编辑部暂无已发布快讯。';
                return;
            }
            const lead = rows[0];
            front.querySelector('[data-wc-title]').textContent = lead.title;
            front.querySelector('[data-wc-summary]').textContent = lead.summary;
            const story = front.querySelector('[data-wc-story]');
            story.href = lead.url;
            story.textContent = '阅读全文　→';
            rail.replaceChildren(...rows.map(row => {
                const a = document.createElement('a');
                a.href = row.url;
                a.textContent = row.title;
                return a;
            }));
        } catch {
            rail.textContent = '快讯暂时无法载入，可从新闻版继续阅读。';
        }
    }
    wcLoadNews();
})();
