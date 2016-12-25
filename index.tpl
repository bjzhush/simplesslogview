<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ECharts</title>
    <script src="./echarts.min.js"></script>
</head>
<body>
<div align="center">
    <h3>SS使用量统计</h3>
    <h4>注：指数值为每天每分钟连接数总和</h4>
    <div id="main" style="width: 1400px;height:640px;"></div>
</div>
<script type="text/javascript">
    var myChart = echarts.init(document.getElementById('main'));


    var dataBJ = [
        [1,55,12,1],
        [2,25,12,2],
        [3,56,12,3],
    ];

    var dataGZ = [
        [1,26,12,1],
        [2,85,12,2],
    ];


    var schema = [
        {name: 'date', index: 0, text: '日'},
        {name: 'AQIindex', index: 1, text: '指数'},
    ];


    var itemStyle = {
        normal: {
            opacity: 0.8,
            shadowBlur: 10,
            shadowOffsetX: 0,
            shadowOffsetY: 0,
            shadowColor: 'rgba(0, 0, 0, 0.5)'
        }
    };

    option = {
        backgroundColor: '#404a59',
        color: [
            '#dd4444', '#fec42c', '#80F1BE'
        ],
        legend: {
            y: 'top',
            data: <?php echo $titleJson; ?>,
            textStyle: {
                color: '#fff',
                fontSize: 16
            }
        },
        grid: {
            x: '10%',
            x2: 150,
            y: '18%',
            y2: '10%'
        },
        tooltip: {
            padding: 10,
            backgroundColor: '#222',
            borderColor: '#777',
            borderWidth: 1,
            formatter: function (obj) {
                var value = obj.value;
                return '<div style="border-bottom: 1px solid rgba(255,255,255,.3); font-size: 18px;padding-bottom: 7px;margin-bottom: 7px">'
                        + obj.seriesName + ' ' + value[2] +'月' + value[3] + '日'
                        + '</div>'
                        + schema[1].text + '：' + value[1] + '<br>';
            }
        },
        xAxis: {
            type: 'value',
            name: '日期',
            nameGap: 16,
            nameTextStyle: {
                color: '#fff',
                fontSize: 14
            },
            max: 32,
            splitLine: {
                show: false
            },
            axisLine: {
                lineStyle: {
                    color: '#eee'
                }
            }
        },
        yAxis: {
            type: 'value',
            name: '',
            nameLocation: 'end',
            nameGap: 20,
            nameTextStyle: {
                color: '#fff',
                fontSize: 16
            },
            axisLine: {
                lineStyle: {
                    color: '#eee'
                }
            },
            splitLine: {
                show: false
            }
        },
        visualMap: [
            {
                left: 'right',
                top: '10%',
                dimension: 1,
                min: 0,
                max: <?php echo $maxValue;?>,
                itemWidth: 30,
                itemHeight: 120,
                calculable: true,
                precision: 0.1,
                text: ['圆形大小：端口总和'],
                textGap: 30,
                textStyle: {
                    color: '#fff'
                },
                inRange: {
                    symbolSize: [10, 70]
                },
                outOfRange: {
                    symbolSize: [10, 70],
                    color: ['rgba(255,255,255,.2)']
                },
                controller: {
                    inRange: {
                        color: ['#c23531']
                    },
                    outOfRange: {
                        color: ['#444']
                    }
                }
            }
        ],
        series: [
                <?php
                echo $jsonData;
                ?>
        ]
    };

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
</script>
</body>
</html>
