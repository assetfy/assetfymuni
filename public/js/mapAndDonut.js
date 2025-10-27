(function() {
    if (typeof window.ApexCharts !== 'undefined') {
        window.initializeMapAndDonutChart = function() {
            const lat = window.lat;
            const long = window.long;

            // Intentar parsear donutData si es una cadena de texto
            if (typeof window.donutData === 'string') {
                try {
                    window.donutData = JSON.parse(window.donutData);
                } catch (error) {
                    console.error('Error al parsear donutData:', error);
                    return;
                }
            }

            // Verificar si donutData es un objeto válido
            if (typeof window.donutData !== 'object' || window.donutData === null) {
                console.error('donutData no está definido o no es un objeto válido.', window.donutData);
                return;
            }

            console.log('Datos asignados para Donut:', window.donutData);

            // Inicialización del mapa
            const mapElement = document.getElementById('map');
            if (mapElement) {
                if (window.map && window.map instanceof L.Map) {
                    window.map.off();
                    window.map.remove();
                }

                if (lat && long) {
                    window.map = L.map('map').setView([lat, long], 18);
                    L.marker([lat, long], { title: 'Ubicación de la empresa', color: 'red' }).addTo(window.map);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(window.map);

                    setTimeout(() => {
                        window.map.invalidateSize();
                    }, 500);
                }
            }

            // Inicialización del gráfico Donut
            const donutChartElement = document.querySelector("#chart");

            if (!donutChartElement) {
                console.error("Elemento para el gráfico Donut no encontrado.");
                return;
            }

            const donutData = window.donutData;

            console.log('Datos recibidos para Donut:', donutData);

            const labels = [];
            const series = [];

            // Verificación de los datos y adición de series al gráfico
            if (donutData.serviciosRealizados > 0) {
                labels.push('Servicios Realizados');
                series.push(donutData.serviciosRealizados);
            }
            if (donutData.serviciosPendienteVisita > 0) {
                labels.push('Pendientes de Visita');
                series.push(donutData.serviciosPendienteVisita);
            }
            if (donutData.serviciosPendientesCotizacion > 0) {
                labels.push('Pendientes de Cotización');
                series.push(donutData.serviciosPendientesCotizacion);
            }
            if (donutData.serviciosCotizadosyEsperando > 0) {
                labels.push('Cotizados y Esperando');
                series.push(donutData.serviciosCotizadosyEsperando);
            }

            // Si no hay datos válidos, mostrar un mensaje
            if (series.length > 0) {
                const optionsDonut = {
                    series: series,
                    chart: {
                        type: 'donut',
                        height: 400,
                        width: '100%'
                    },
                    labels: labels,
                    dataLabels: {
                        enabled: false
                    },
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function (val) {
                                const total = series.reduce((a, b) => a + b, 0);
                                const percentage = Math.round(val / total * 100) + '%';
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
                            const total = series.reduce((a, b) => a + b, 0);
                            const percentage = Math.round(val / total * 100) + '%';
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
        };
    } else {
        console.error('ApexCharts no está disponible en window.');
    }
})();
