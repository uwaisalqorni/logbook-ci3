<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Grafik Logbook per Unit</h3>
    </div>
    <div class="card-body">
        <form action="" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Diagram Batang (Bar Chart)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Diagram Lingkaran (Pie Chart)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

<!-- ChartJS -->
<script src="<?php echo base_url(); ?>assets/adminLTE/plugins/chart.js/Chart.min.js"></script>

<script>
    $(function () {
        var areaChartData = {
            labels  : <?php echo $data['labels']; ?>,
            datasets: [
                {
                    label               : 'Jumlah Logbook',
                    backgroundColor     : 'rgba(60,141,188,0.9)',
                    borderColor         : 'rgba(60,141,188,0.8)',
                    pointRadius          : false,
                    pointColor          : '#3b8bba',
                    pointStrokeColor    : 'rgba(60,141,188,1)',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data                : <?php echo $data['data_counts']; ?>
                }
            ]
        }

        //-------------
        //- BAR CHART -
        //-------------
        var barChartCanvas = $('#barChart').get(0).getContext('2d')
        var barChartData = $.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        barChartData.datasets[0] = temp0

        var barChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }]
            }
        }

        new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        })

        //-------------
        //- PIE CHART -
        //-------------
        var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
        var pieData        = {
            labels: <?php echo $data['labels']; ?>,
            datasets: [
                {
                    data: <?php echo $data['data_counts']; ?>,
                    backgroundColor : <?php echo $data['background_colors']; ?>,
                }
            ]
        }
        var pieOptions     = {
            maintainAspectRatio : false,
            responsive : true,
        }

        new Chart(pieChartCanvas, {
            type: 'pie',
            data: pieData,
            options: pieOptions
        })
    })
</script>
