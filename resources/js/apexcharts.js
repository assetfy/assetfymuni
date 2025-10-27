import ApexCharts from 'apexcharts';

function initBarCharts() {

    const solicitudesChartElement = document.querySelector("#solicitudesChart");
    const serviciosPeticionesPorMesChartElement = document.querySelector("#serviciosPeticionesPorMesChart");

    if (!solicitudesChartElement || !serviciosPeticionesPorMesChartElement) {
        console.error("Elementos para los gráficos de barras no encontrados.");
        return;
    }

    // Reasignación explícita de los datos justo antes de inicializar los gráficos
    const solicitudesAprobadasData = window.solicitudesAprobadasData;
    const solicitudesPendientesData = window.solicitudesPendientesData;
    const serviciosCount = window.serviciosCount;
    const peticionesCount = window.peticionesCount;

    const commonOptions = {
        chart: {
            type: 'bar',
            height: 350,
            width: '100%'
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '50%'
            }
        },
        yaxis: {
            min: 0,
            labels: {
                formatter: function (val) {
                    return Math.round(val);
                }
            },
            title: {
                text: 'Cantidad'
            }
        }
    };

    var optionsSolicitudes = {
        ...commonOptions,
        series: [
            {
                name: 'Solicitudes Aprobadas',
                data: [solicitudesAprobadasData]
            },
            {
                name: 'Solicitudes Pendientes',
                data: [solicitudesPendientesData]
            }
        ],
        xaxis: {
            categories: ['Solicitudes'],
            title: {
                text: 'Tipo de Solicitud'
            }
        },
        yaxis: {
            ...commonOptions.yaxis,
            max: Math.max(solicitudesAprobadasData, solicitudesPendientesData) + 1,
            tickAmount: Math.max(solicitudesAprobadasData, solicitudesPendientesData) + 1
        }
    };

    window.chartSolicitudes = new ApexCharts(solicitudesChartElement, optionsSolicitudes);
    window.chartSolicitudes.render();

    var optionsServiciosPeticionesPorMes = {
        ...commonOptions,
        series: [
            {
                name: 'Servicios en ' + window.currentMonth,
                data: [serviciosCount]
            },
            {
                name: 'Peticiones en ' + window.currentMonth,
                data: [peticionesCount]
            }
        ],
        xaxis: {
            categories: [window.currentMonth],
            title: {
                text: 'Mes'
            }
        },
        yaxis: {
            ...commonOptions.yaxis,
            max: Math.max(serviciosCount, peticionesCount) + 1,
            tickAmount: Math.max(serviciosCount, peticionesCount) + 1
        }
    };

    window.chartServiciosPeticionesPorMes = new ApexCharts(serviciosPeticionesPorMesChartElement, optionsServiciosPeticionesPorMes);
    window.chartServiciosPeticionesPorMes.render();
}

function initDonutChart() {

    const donutChartElement = document.querySelector("#chart");

    if (!donutChartElement) {
        console.error("Elemento para el gráfico Donut no encontrado.");
        return;
    }

    // Reasignación explícita de los datos justo antes de inicializar el gráfico
    const serviciosRealizados = window.serviciosRealizados;
    const serviciosPendienteVisita = window.serviciosPendienteVisita;
    const serviciosPendientesCotizacion = window.serviciosPendientesCotizacion;
    const serviciosCotizadosyEsperando = window.serviciosCotizadosyEsperando;

    const seriesValues = [
        serviciosRealizados,
        serviciosPendienteVisita,
        serviciosPendientesCotizacion,
        serviciosCotizadosyEsperando
    ];

    const totalServicios = seriesValues.reduce((a, b) => a + b, 0);
    console.log('Total de servicios:', totalServicios);

    const hasValidData = totalServicios > 0;
    console.log('Verificación de datos válidos para Donut:', hasValidData);

    if (hasValidData) {
        const optionsDonut = {
            series: seriesValues,
            chart: {
                type: 'donut',
                height: 400,
                width: '100%'
            },
            labels: ['Servicios Realizados', 'Pendientes de Visita', 'Pendientes de Cotización', 'Cotizados y Esperando'],
            dataLabels: {
                enabled: false
            },
            tooltip: {
                enabled: true,
                y: {
                    formatter: function (val, { seriesIndex, w }) {
                        const percentage = Math.round(val / totalServicios * 100) + '%';
                        return `${val} (${percentage})`;
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                    }
                }
            },
            responsive: [{
                breakpoint: 580,
                options: {
                    chart: {
                        width: '100%'
                    },
                    legend: {
                        show: true,
                        position: 'bottom'
                    }
                }
            }],
            legend: {
                position: 'bottom',
                offsetY: 0,
                horizontalAlign: 'center',
                markers: {
                    width: 10,
                    height: 10,
                    radius: 12
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                },
                formatter: function(seriesName, opts) {
                    const val = opts.w.globals.series[opts.seriesIndex];
                    const percentage = Math.round(val / totalServicios * 100) + '%';
                    return `${seriesName}: ${percentage}`;
                }
            }
        };

        window.chartDonut = new ApexCharts(donutChartElement, optionsDonut);
        window.chartDonut.render();
    } else {
        console.warn("No hay datos válidos para renderizar el gráfico Donut.");
        donutChartElement.innerHTML = '<p>No hay datos disponibles para mostrar en este gráfico.</p>';
    }
}

window.initBarCharts = initBarCharts;
window.initDonutChart = initDonutChart;

export {
    initBarCharts,
    initDonutChart
};
