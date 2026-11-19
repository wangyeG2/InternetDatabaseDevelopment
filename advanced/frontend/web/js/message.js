// Progressive enhancement for the public message board. Maintainer: D (秦东杰).
'use strict';

const messageBody = document.querySelector('[data-message-body]');
const messageLength = document.querySelector('[data-message-length]');
if (messageBody && messageLength) {
    const min = Number(messageBody.dataset.messageMin);
    const max = Number(messageBody.dataset.messageMax);
    const updateLength = () => {
        const length = Array.from(messageBody.value.trim()).length;
        messageLength.textContent = ` · ${length} / ${max}`;
        let error = '';
        if (length > max) {
            error = `留言最多允许 ${max} 个字符。`;
        } else if (length > 0 && length < min) {
            error = `留言至少需要 ${min} 个字符。`;
        }
        messageBody.setCustomValidity(error);
    };
    messageBody.addEventListener('input', updateLength);
    updateLength();
}

const messageChart = document.querySelector('[data-message-chart]');
if (messageChart) {
    const controls = messageChart.querySelector('[data-chart-controls]');
    const buttons = Array.from(controls.querySelectorAll('[data-chart-mode]'));
    const rows = Array.from(messageChart.querySelectorAll('[data-chart-count]'));
    const counts = rows.map(row => Number(row.dataset.chartCount));
    const total = counts.reduce((sum, count) => sum + count, 0);
    const peak = Math.max(1, ...counts);
    const summary = messageChart.querySelector('[data-chart-summary]');
    const percent = new Intl.NumberFormat('zh-CN', { style: 'percent', maximumFractionDigits: 1 });

    controls.hidden = false;
    controls.addEventListener('click', event => {
        if (!buttons.includes(event.target)) {
            return;
        }
        const byShare = event.target.dataset.chartMode === 'share';
        for (const button of buttons) {
            const active = button === event.target;
            button.classList.toggle('active', active);
            button.setAttribute('aria-pressed', String(active));
        }
        rows.forEach((row, index) => {
            const count = counts[index];
            const share = count / Math.max(1, total);
            row.querySelector('[data-chart-value]').textContent = byShare ? percent.format(share) : `${count} 条`;
            row.querySelector('.message-track span').style.setProperty('--bar-width', `${100 * (byShare ? share : count / peak)}%`);
        });
        summary.textContent = `已公开 ${total} 条留言，按话题显示${byShare ? '占比' : '条数'}。`;
    });
}
