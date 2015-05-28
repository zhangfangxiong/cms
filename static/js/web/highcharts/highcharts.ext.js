/*
下面的这些代码会输出走势图

支持单双走势图

prices,name prices代表一个走势数据name代表其名称
prices2,name2 prices2代表另一个走势数据name2代表其名称
container代表绑定元素
width, height分别代表走势图的宽高
y, m, d分别代表走势图的X轴对应的起始年,月,日

PS:1.若prices2=[],name2=''则代表空走势
   2.图例默认不显示
*/
window.DrawLineChartExt = function (prices, name, prices2, name2, container, width, height, y, m, d, legend) {
    name = name || '';
    name2 = name2 || '';

    width = width || 10;
    height = height || 25;
    y = y || 2013;
    m = m || 1;
    d = d || 1;

    legend = legend || false;

    if (prices.length == 0) {
        prices = [21115, 20452, 20052,
        21446, 20106, 19846, 20424,
        21047, 21115, 19823, 21146, 
        21213, 21485, 21592, 21500, 
        21491, 21478, 21612, 21193];
    }
    var labels = [];
    var values = [];
    var values2 = [];
    var reversePrices = prices;
    if (typeof (reversePrices) != "undefined") {
        reversePrices = prices.reverse();
    }

    var reversePrices2 = prices2;
    if (typeof (reversePrices2) != "undefined") {
        reversePrices2 = prices2.reverse();
    }

    var priceLength = reversePrices.length < reversePrices2.length ? reversePrices.length : reversePrices2.length;

    var date = new Date();
    var j = 1;

    for (var i = 0; i < priceLength; i++) {
        if (i % 4 == 0 || i == 0) {
            var p = reversePrices[i];
            if (typeof (p) != "undefined") {
                p = parseInt(p);
                values.push(p);
            }

            var p2 = reversePrices2[i];
            if (typeof (p2) != "undefined") {
                p2 = parseInt(p2);
                values2.push(p2);
            }

            date.setFullYear(y);
            date.setMonth(m - j);
            labels.push(date.getFullYear() + "/" + (date.getMonth() + 1));
            j++;
        }
        if (j > 12) {
            break;
        }
    }

    var minPrice = 99999999, maxPrice = 0;
    minPrice = Math.min.apply(Math, values);
    maxPrice = Math.max.apply(Math, values);

    values = values.reverse();
    values2 = values2.reverse();
    labels = labels.reverse();

    var price = maxPrice * 1.1;
    price = price - price % 100;
    while (price < maxPrice) {
        price += 100;
    }
    maxPrice = price;

    price = minPrice * 0.9;
    price = price - price % 100;
    minPrice = price;

    var chart = new Highcharts.Chart({
        colors: ['#34B0F8', '#81C33A'],
        chart: {
            renderTo: container,
            type: 'line'
        },
        title: {
            text: '',
            style: {
                color: '#333',
                font: '600 16px Arial,宋体,Helvetica,sans-serif'
            }
        },
        xAxis: {
            categories: labels,
            labels: {
                step: (priceLength > 28 ? 2 : 1)
            }
        },
        yAxis: {
            title: {
                text: '',
                style: {
                    color: '#666',
                    fontWeight: '300',
                    fontSize: '12px',
                    fontFamily: 'Arial,宋体,Helvetica,sans-serif'
                }
            },
            minorTickInterval: (maxPrice - minPrice) / 100,
            lineColor: '#ccc',
            tickPosition: 'inside',
            tickColor: '#ccc',
            labels: {
                formatter: function () {
                    return (this.value / 10000).toFixed(1) + "万";
                },
                style: {
                    color: '#333',
                    font: '11px Trebuchet MS, Verdana, sans-serif'
                }
            }
        },
        legend: {
            enabled: legend//图例是否显示
        },
        series: [{
            name: name,
            data: values
        }, {
            name: name2,
            data: values2
        }]
    });
};

window.DrawAreaChartExt = function (obj) {
    var container = obj.option;
    var width = obj.width;
    var height = obj.height;
    var prices = obj.prices;
    var y = obj.year;
    var m = obj.month;

    width = width || 405;
    height = height || 130;

    if (prices.length == 0) {
        prices = [21115, 20452, 20052,
        21446, 20106, 19846, 20424,
        21047, 21115, 19823, 21146,
        21213, 21485, 21592, 21500,
        21491, 21478, 21612, 21193];
    }
    var labels = [];
    var values = [];
    var reversePrices = prices;
    if (typeof (reversePrices) != "undefined") {
        reversePrices = prices.reverse();
    }

    var priceLength = reversePrices.length;

    var date = new Date();
    var j = 1;

    for (var i = 0; i < priceLength; i++) {
        if (i % 4 == 0 || i == 0) {
            var p = reversePrices[i];
            if (typeof (p) != "undefined") {
                p = parseInt(p);
                values.push(p);
            }


            date.setFullYear(y);
            date.setMonth(m - j);
            labels.push(date.getFullYear() + "/" + (date.getMonth() + 1));
            j++;
        }
        if (j > 12) {
            break;
        }
    }

    var minPrice = 99999999, maxPrice = 0;
    minPrice = Math.min.apply(Math, values);
    maxPrice = Math.max.apply(Math, values);

    values = values.reverse();
    labels = labels.reverse();

    var price = maxPrice * 1.1;
    price = price - price % 100;
    while (price < maxPrice) {
        price += 100;
    }
    maxPrice = price;

    price = minPrice * 0.9;
    price = price - price % 100;
    minPrice = price;
    var chart = new Highcharts.Chart({
        colors: ['#618CB4', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
        chart: {
            renderTo: container,
            type: 'areaspline',
            borderColor: '#618CB4',
            width: width,
            height: height,
            backgroundColor: '#fff',
            borderWidth: 0,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: false
        },
        title: {
            text: '',
            style: {
                color: '#333',
                font: '600 16px Arial,宋体,Helvetica,sans-serif'
            }
        },
        subtitle: {
            text: "",
            style: {
                color: '#666',
                font: '600 12px Arial,宋体,Helvetica,sans-serif'
            }
        },
        xAxis: {
            gridLineWidth: 1,
            lineColor: '#ccc',
            tickColor: '#ccc',
            tickPosition: 'inside',
            type: 'string',
            labels: {
                formatter: function () {
                    return labels[this.value];
                },
                style: {
                    color: '#666',
                    font: '12px Arial,宋体,Helvetica,sans-serif'
                },
                title: {
                    style: {
                        color: '#666',
                        fontWeight: '300',
                        fontSize: '12px',
                        fontFamily: 'Arial,宋体,Helvetica,sans-serif'
                    }
                },
                y: 20
            }
        },
        yAxis: {
            title: {
                text: '',
                style: {
                    color: '#666',
                    fontWeight: '300',
                    fontSize: '12px',
                    fontFamily: 'Arial,宋体,Helvetica,sans-serif'
                }
            },
            min: minPrice,
            max: maxPrice,
            minorTickInterval: (maxPrice - minPrice) / 100,
            lineColor: '#ccc',
            lineWidth: 0,
            tickWidth: 0,
            tickPosition: 'inside',
            tickColor: '#ccc',
            labels: {
                formatter: function () {
                    return (this.value / 10000).toFixed(1) + "万";
                },
                style: {
                    color: '#333',
                    font: '11px Trebuchet MS, Verdana, sans-serif'
                }
            }
        },
        tooltip: {
            formatter: function () {
                return this.y + "元";
            }
        },
        exporting: {
            enabled: false
        },
        legend: {
            enabled: false
        },
        navigation: {
            buttonOptions: {
                theme: {
                    stroke: '#ccc'
                }
            }
        },
        labels: {
            style: {
                color: '#99b'
            }
        },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.1
            }
        },
        series: [{
            name: '',
            showInLegend: false,
            data: values,
            marker: {
                fillColor: '#fff',
                lineWidth: 1,
                lineColor: Highcharts.getOptions().colors[0]
            }
        }]
    });
};

window.DrawColumnNegative = function (container, name, value, width, height) {
    var categories = [];
    var lables = [];
    var splineData = [];
    var columnData = [];
    for (var i = 0; i < value.length; i++) {
        categories.push(value[i].Menu);
        lables.push(value[i].Label);
        splineData.push(parseFloat(value[0].Price));
        columnData.push(parseFloat(value[i].Price));
    }
    
    name = name || "";
    var minPrice = Math.min.apply(Math, columnData);
    var maxPrice = Math.max.apply(Math, columnData);
    var chart = new Highcharts.Chart({
        colors: ['#CCCCCC', '#0099ff'],
        chart: {
            renderTo: container
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: categories
        },
        yAxis: {
            labels: {
                formatter: function () {
                    return (this.value / 10000).toFixed(1) + "万";
                },
                style: {
                    color: '#333',
                    font: '11px Trebuchet MS, Verdana, sans-serif'
                }
            }
        },
        plotOptions: {
            series: {
                marker: {
                    radius: 0,  //曲线点半径，默认是4
                    symbol: 'diamond' //曲线点类型："circle", "square", "diamond", "triangle","triangle-down"，默认是"circle"
                }
            }
        },
        tooltip: {
            formatter: function () {
                return lables[this.point.x];
            }
        },
        labels: {
            items: [{
                html: '',
                style: {
                    left: '40px',
                    top: '8px',
                    color: 'black'
                }
            }]
        },
        series: [{
            type: 'column',
            name: '',
            data: columnData,
            showInLegend: false
        }, {
            type: 'spline',
            name: '',
            data: splineData,
            showInLegend: false
        }]
    });
};

window.DrawPriceCompareChart = function (container, width, height, price) {
    width = width || 270;
    height = height || 144;
    var title = "";
    var s = [];

    var footLabels = [""];
    var labels = [""];
    var minPrice = price.Price, maxPrice = price.Price;
    for (var i = 4; i < arguments.length; i++) {
        var p = arguments[i];
        labels.push(p.Label);
        footLabels.push(p.Foot);
        p = p.Price;
        if (p == null) {
            break;
        }
        minPrice = Math.min(p, minPrice);
        maxPrice = Math.max(p, maxPrice);
        s.push({
            showInLegend: false,
            marker: {
                symbol: 'square'
            },
            data: [{
                x: s.length + 1,
                y: p,
                marker: {
                    symbol: 'url(http://res.fangjia.cric.com/images/icon_yy.png)'
                }
            }]

        });
    }
    labels[0] = price.Label;
    labels.push(price.Label);
    footLabels[0] = price.Foot;
    footLabels.push(price.Foot);
    var val2 = [];
    val2.push({
        y: price.Price,
        marker: {
            symbol: 'url(http://res.fangjia.cric.com/images/icon_yy.png)'
        }
    });
    val2.push({
        x: s.length + 1,
        y: price.Price
    });
    s.push({
        showInLegend: false,
        data: val2
    });
    var s2 = [];
    s2.push(s[0]);
    s2.push(s[s.length - 1]);
    for (var i = 1; i < s.length - 1; i++) {
        s2.push(s[i]);
    }
    s = s2;

    var price = maxPrice * 1.1;
    price = price - price % 100;
    while (price < maxPrice) {
        price += 100;
    }
    maxPrice = price;
    var price = minPrice * 0.9;
    price = price - price % 100;
    minPrice = price;
    var chart = new Highcharts.Chart({
        colors: ['#fff', '#618CB4'],
        chart: {
            renderTo: container,
            type: 'line',
            borderColor: '#618CB4',
            width: width,
            height: height,
            backgroundColor: '#fff',
            borderWidth: 0,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: false
        },
        title: {
            text: ''
        },
        yAxis: {
            title: {
                text: ''
            },
            labels: {
                formatter: function () {
                    return (this.value / 10000).toFixed(1) + "万";
                }
            },
            min: minPrice,
            max: maxPrice
        },
        xAxis: {
            type: 'string',
            labels: {
                formatter: function () {
                    return footLabels[this.value];
                }
            }
        },
        tooltip: {
            formatter: function () {
                var label = labels[this.x];
                return label + "　　";
            }
        },
        exporting: {
            enabled: false
        },
        plotOptions: {
            spline: {
                marker: {
                    radius: 1,
                    lineColor: '#666666',
                    lineWidth: 0
                }
            },
            showInLegend: false
        },
        series: s
    });
}

window.DrawPieChart = function (container, width, height, values) {
    width = width || 270;
    height = height || 144;
    var title = "";
    var chart = new Highcharts.Chart({
        chart: {
            renderTo: container,
            type: 'pie',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        colors: ['#82abd8','#d5e5f7','#4087d6'],
        title: {
            text: title
        },
        tooltip: {
            headerFormat: '',
            pointFormat: '{point.name}: <b>{point.percentage}%</b>',
            percentageDecimals: 2
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    formatter: function () {
                        return this.percentage.toFixed(2) + ' %';
                    }
                },
                showInLegend: false
            }
        },
        series: [{
            data: values
        }]
    });

         };

window.DrawLineChart = function (prices, id, xAxis, data, width, height, y, m, d, flagText) {
    width = width || 100;
    height = height || 100;
    y = y || 2014;
    m = m || 1;
    d = d || 1;
    var title = "";
    if (flagText != "") {
        title = flagText;
    }
    if (prices == 0) {
        prices = [21115, 20452, 20052, 20217, 21318, 20854, 20342, 20126, 21320, 20155, 20883, 20065, 20396, 19839, 21169, 20272, 20896, 21008, 21048, 21062, 20053, 20473, 21766, 19891, 20175, 20373, 21766, 20003, 21167, 20010, 21031, 21216, 20117, 19905, 19763, 20134, 19828, 20761, 21446, 20106, 19846, 20424, 21047, 21115, 19823, 21146, 21213, 21485, 21592, 21500, 21491, 21478, 21612, 21193];
    }
    var result = [];

    var labels = [];
    var date = new Date();
    var reversePrices = prices.reverse();
    var minPrice = 99999999;
    var maxPrice = 0;
    var x = 1;
    for (var i = 0; i < reversePrices.length; i++) {
        if (i % 4 == 0 || i == 0) {
            var values = parseInt(reversePrices[i]);
            result.push(values);
            date.setFullYear(y);
            date.setMonth(m -x);

            labels.push(date.getFullYear() + "/" + (date.getMonth() +1));
            minPrice = Math.min(values, minPrice);
            maxPrice = Math.max(values, maxPrice);
            x++;
        }
        if (x > 12) {
            break;
        }
    }

    result = result.reverse();
	labels = labels.reverse();

    var chart = new Highcharts.Chart({
        chart: {
            renderTo: id,
            type: 'line',
            width: width,
            height: height
        },
        title: {
            text: title
        },
		colors:['#ee4d00'],
        xAxis: {
            gridLineWidth: 1,
            lineColor: '#ccc',
            tickColor: '#ccc',
            tickPosition: 'inside',
            type: 'string',
            labels: {
                formatter: function () {
                    return labels[this.value];
                },
                style: {
                    color: '#666',
                    font: '12px Arial,宋体,Helvetica,sans-serif'
                },
                title: {
                    style: {
                        color: '#666',
                        fontWeight: '300',
                        fontSize: '12px',
                        fontFamily: 'Arial,宋体,Helvetica,sans-serif'
                    }
                },
                y: 20
            }
        },
        yAxis: {
            title: {
                text: '',
                style: {
                    color: '#666',
                    fontWeight: '300',
                    fontSize: '12px',
                    fontFamily: 'Arial,宋体,Helvetica,sans-serif'
                }
            },
            min: minPrice,
            max: maxPrice,
            minorTickInterval: (maxPrice - minPrice) / 100,
          //  lineColor: '#eb6100',
            lineWidth: 0,
            tickWidth: 0,
            tickPosition: 'inside',
          //  tickColor: '#ccc',
            //   tickInterval:3000,
            labels: {
                formatter: function () {
                    return this.value;
                },
                style: {
                    color: '#333',
                    font: '11px Trebuchet MS, Verdana, sans-serif'
                }
            }
            
        },
        legend:{enabled:false},
        tooltip: {
            formatter: function () {
                return this.y + "元";
            }
        },
        series: [{
            name: '价格走势',
            data: result
        }]
    });
}



var DrawLoanPieCircle = function (container,data) {
    $('#' + container).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: false
                },
                startAngle: -180,
                endAngle: 180,
                center: ['50%', '50%']
            }
        },
        series: [{
            type: 'pie',
            name: '',
            innerSize: '70%',
            data:data
        }],
        colors: ["#81c33a", "#387dbd", "#dc4c4f"],
        credits: { enabled: false }
    });
}

window.DrawMixtureChart = function (id, width, height, dataParam, y, m, d) {
    if (dataParam.length == 0)
        return;

    width = width || 1068;
    height = height || 255;
    y = y || 2015;
    m = m || 1;
    d = d || 1;

    var dt = new Date(y, m, d);

    var labels = []; //月份

    for (var i = 12; i > 0 ; i--) {
        dt.setFullYear(y);
        dt.setMonth(m - i);
        //labels.push(dt.getFullYear() + "/" + (dt.getMonth() + 1));
        labels.push((dt.getMonth() + 1) + "月");
    }

    var minPrice = 99999999, maxPrice = 0;

    for (var i = 0 ; i < dataParam.length; i++) {
        var temArr = new Array();
        for (var j = 0; j < dataParam[i].data.length; j++) {
            if (dataParam[i].data[j]) {
                temArr.push(dataParam[i].data[j]);
            }
        }

        if (minPrice > Math.min.apply(Math, temArr)) {
            minPrice = Math.min.apply(Math, temArr);
        }
        if (maxPrice < Math.max.apply(Math, temArr)) {
            maxPrice = Math.max.apply(Math, temArr);
        }
    }

    var price = maxPrice * 1.1;
    price = price - price % 100;
    while (price < maxPrice) {
        price += 100;
    }
    maxPrice = price;
    price = minPrice * 0.9;
    price = price - price % 100;
    minPrice = price;
    $("#" + id).highcharts({
        title: { text: '' },
        credits: { enabled: false },
        xAxis: {
            categories: labels,
            minorTickInterval: 1
        },
        yAxis: {
            title: {
                text: ''
            },
            max: maxPrice,
            min: minPrice,
            gridLineWidth: 1,
            labels: {
                formatter: function () {
                    var s = formatPrice(this.value);
                    return s + "元";
                }
            }
        },
        legend: {
            enabled: false
        },
        series: dataParam
    });
}

window.DrawMixtureChart2 = function (id, width, height, dataParam, y, m, d) {
    if (dataParam.length == 0)
        return;

    width = width || 710;
    height = height || 255;
    y = y || 2015;
    m = m || 1;
    d = d || 1;

    var dt = new Date(y, m, d);

    var labels = [];

    for (var i = 4; i > 0 ; i--) {
        dt.setFullYear(y);
        dt.setMonth(m - i);
        labels.push((dt.getMonth() + 1) + "月");
    }

    var minPrice = 9999, maxPrice = 0;

    var minCounts = 99999, maxCounts = 0;

    for (var i = 0 ; i < dataParam.length; i++) {
        if (dataParam[i].name.indexOf("售价") > -1) {
            var data = dataParam[i].data;
            for (j = 0 ; j < data.length; j++) {
                if (data[j]) {
                    if (data[j].y) {
                        if (minPrice > data[j].y) {
                            minPrice = data[j].y;
                        }
                        if (maxPrice < data[j].y) {
                            maxPrice = data[j].y;
                        }
                    }
                }
            }
        }
        else if (dataParam[i].name.indexOf("套数") > -1) {
            var data = dataParam[i].data;
            for (j = 0; j < data.length; j++) {
                if (data[j]) {
                    if (minCounts > data[j]) {
                        minCounts = data[j];
                    }
                    if (maxCounts < data[j]) {
                        maxCounts = data[j];
                    }
                }
            }
        }
    }

    //alert("minCounts:" + minCounts + "，maxCounts:" + maxCounts);

    //var price = maxPrice * 1.1;
    //price = price - price % 100.0;
    //while (price < maxPrice) {
    //    price += 1;
    //} 
    //maxPrice = price; 
    price = minPrice * 0.9;
    price = price - price % 100.0;
    minPrice = price;

    $("#" + id).highcharts({
        title: { text: '' },
        credits: { enabled: false },
        tooltip: { enabled: false },
        plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    formatter: function () {
                        var seriesName = this.series.name;

                        if (seriesName == "售价") {
                            if (this.y == null) {
                                return "";
                            }
                            return (this.y).toFixed("1") + "万";
                        }

                        return this.y + "套";
                    }
                }
            },
            line: {
                marker: {
                    radius: 5  //曲线点半径，默认是4
                },
                dataLabels: {
                    color: "#F7A633"
                },
                enableMouseTracking: false
            },
            column: {
                dataLabels: {
                    color: "#E14C4E"
                },
                enableMouseTracking: false,
                pointWidth: 40
            }
        },
        xAxis: {
            categories: labels,
            minorTickInterval: 1
        },
        yAxis: [{
            title: {
                text: ''
            },
            gridLineWidth: 0,
            max: maxPrice,
            min: minPrice,
            allowDecimals: true,
            labels: {
                enabled: false
            }
        },
            {
                title: {
                    text: ''
                },
                gridLineWidth: 0,
                max: maxCounts * 2,
                min: 0,
                allowDecimals: true,
                labels: {
                    enabled: false
                }
            }],
        legend: {
            enabled: false
        },
        series: dataParam
    });
}

