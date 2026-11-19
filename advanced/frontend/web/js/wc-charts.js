/* 世界杯数据版 · 秦东杰 */
(() => {
    const script = document.getElementById('wc-chart-data');
    if (!script || !window.echarts) return;
    const data = JSON.parse(script.textContent);
    const colors = ['#37715c','#a54d33','#a98335','#6685a0','#88719a','#799840'];
    const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.querySelectorAll('[data-wc-chart]').forEach(element => {
        const key = element.dataset.wcChart;
        const values = data[key];
        const names = Object.keys(values);
        const chart = echarts.init(element);
        const option = {animation: !reduceMotion, color: colors, tooltip: {trigger:'item'}};
        if (key === 'teams') {
            option.legend = {bottom:0,textStyle:{color:'#708078'}};
            option.series = [{type:'pie',radius:['32%','60%'],center:['50%','42%'],
                label:{show:false},data:names.map(name => ({name,value:values[name]}))}];
        } else {
            option.grid = {left:8,right:12,top:18,bottom:8,containLabel:true};
            option.xAxis = {type:'category',data:names,axisLabel:{color:'#708078'}};
            option.yAxis = {type:'value',minInterval:1,axisLabel:{color:'#708078'}};
            option.series = [{type:key==='trend'?'line':'bar',barMaxWidth:32,data:Object.values(values)}];
        }
        chart.setOption(option);
        new ResizeObserver(() => chart.resize()).observe(element);
    });
})();
