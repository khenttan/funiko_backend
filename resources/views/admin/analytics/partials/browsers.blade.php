<div class="card card-outline card-primary" id="box-browsers" style="min-height: 400px;">
    <div class="card-header">
        <h4 class="float-left m-0">
            <span><i class="fab fa-line-chart"></i></span>
            <span>Top performing Video</span>
        </h4>

        {{-- @include('admin.partials.boxes.toolbar') --}}
    </div>

    <div class="card-body">
   
        <table id="tbl-devices" data-order-by="1|desc" class="table nowrap table-striped table-sm table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Video</th>
                <th>Uploaded by</th>
                <th>Views</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($post_videos as $video )
                <tr>
                    <td>
                    <figure id="video-viewport" style="max-width: 100px; max-height: 100px;">
                        <video id="video-{{$video['id']}}" width="100px" height="100px" preload="auto" controls muted="" src="{{$video['video']}}">
                            <source src="{{$video['video']}}" type="video/mp4">
                        </video>
                    </figure>
                   </td>
                   <td>{{ $video['user']['username']}}</td>

                    <td>{{ $video['post_view_one']['post_count']}}</td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>

@section('scripts')
    @parent
    <script type="text/javascript" charset="utf-8">
        $(function ()
        {
            var chart;

            initToolbarDateRange('#box-browsers .daterange', updateChart);

            /**
             * Get the chart's data
             * @param view
             */
            function updateChart(start, end)
            {
                if (chart) {
                    chart.destroy();
                }

                if (!start) {
                    start = moment().subtract(29, 'days').format('YYYY-MM-DD');
                    end = moment().format('YYYY-MM-DD');
                }

                $('#box-browsers .loading-widget').show();
                doAjax('/api/analytics/browsers', {
                    'start': start, 'end': end,
                }, createPieChart);
            }

            function createPieChart(data)
            {

                console.log(data);
                // total page views and visitors line chart
                var ctx = document.getElementById("chart-browsers").getContext("2d");

                var chart = new Chart(ctx, {
                    type: 'doughnut',
                    data: data
                });

                 $('#box-browsers .loading-widget').slideUp();
            }

            setTimeout(function ()
            {
                updateChart();
            }, 300);
        })
    </script>
@endsection