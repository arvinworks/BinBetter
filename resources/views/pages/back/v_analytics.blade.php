@extends('layouts.back.app')

@section('content')
<div class="app-content-area pt-0 ">
    <div class="bg-primary pt-12 pb-21 "></div>
    <div class="container-fluid mt-n22 ">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="mb-2 mb-lg-0">
                        <h3 class="mb-0 text-white">{{ $page }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-xl-12 col-12 mb-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Ongoing Events by Location</h4>
                    </div>

                    <div class="card-body">
                        <!-- Display as a table -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Event Name</th>
                                    <th>Location</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Participants Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ongoingEvents as $event)
                                <tr>
                                    <td>{{ $event->id }}</td>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>{{ $event->start_date }}</td>
                                    <td>{{ $event->end_date }}</td>
                                    <td>{{ $event->join_events_count }}</td> <!-- Display the count of joined users -->
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Display as a chart (optional) -->
                        <canvas id="ongoingEventsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-xl-12 col-12 mb-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Registered Users Count (Daily)</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Registered Users Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registeredUsers as $user)
                                <tr>
                                    <td>{{ $user->date }}</td>
                                    <td>{{ $user->count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <canvas id="registeredUsersChart"></canvas>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-12 mb-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Uncleaned Address Report</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($uncleanedAddresses as $address)
                                <tr>
                                    <td>{{ $address->id }}</td>
                                    <td>{{ $address->address }}</td>
                                    <td>Pending</td> <!-- Since these are only uncleaned addresses -->
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No uncleaned addresses found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Chart container -->
                        <canvas id="postReportsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
@endsection

@push('scripts')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const events = JSON.parse('<?php echo json_encode($ongoingEvents) ?>');

    // Check if there is data to display
    const labels = events.length ? events.map(event => event.location) : ['No data'];
    const data = events.length ? events.map(event => event.join_events_count) : [0];

    // Create the chart
    const ctx = document.getElementById('ongoingEventsChart').getContext('2d');
    const ongoingEventsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ongoing Events by Location',
                data: data,
                backgroundColor: events.length ? 'rgba(54, 162, 235, 0.9)' : 'rgba(200, 200, 200, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                barPercentage: 0.1,
                categoryPercentage: 0.7
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(200, 200, 200, 0.3)'
                    },
                    title: {
                        display: true,
                        text: 'Participants Count'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Event Location'
                    }
                }
            },
            elements: {
                bar: {
                    barThickness: 30,
                }
            },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#333'
                    }
                },
                tooltip: {
                    enabled: events.length > 0
                },

                annotation: {
                    drawTime: 'beforeDatasetsDraw',
                    annotations: {
                        noDataText: {
                            type: 'label',
                            content: 'No data found',
                            enabled: events.length === 0,
                            position: {
                                x: '50%',
                                y: '50%'
                            },
                            font: {
                                size: 16,
                                weight: 'bold',
                                family: 'Arial'
                            },
                            color: 'rgba(150, 150, 150, 1)'
                        }
                    }
                }
            }
        },
        plugins: [{
            id: 'noData',
            beforeDraw: (chart) => {
                if (events.length === 0) {
                    const ctx = chart.ctx;
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = 'rgba(150, 150, 150, 1)';
                    ctx.font = '16px Arial';
                    ctx.fillText('No data found', chart.width / 2, chart.height / 2);
                    ctx.restore();
                }
            }
        }]
    });
</script>


<script>
    const registeredUsers = JSON.parse('<?php echo json_encode($registeredUsers) ?>'); // Parse JSON string into JavaScript object

    // Prepare data for the chart
    const labelss = registeredUsers.map(user => user.date); // Use the registration dates as labels
    const datas = registeredUsers.map(user => user.count); // Use the count of users for data

    // Create the chart
    const ctxs = document.getElementById('registeredUsersChart').getContext('2d');
    const registeredUsersChart = new Chart(ctxs, {
        type: 'bar',
        data: {
            labels: labelss,
            datasets: [{
                label: 'Registered Users (Daily)',
                data: datas,
                backgroundColor: events.length ? 'rgba(54, 162, 235, 0.9)' : 'rgba(200, 200, 200, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                barPercentage: 0.1,
                categoryPercentage: 0.7
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.raw + ' users';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            elements: {
                bar: {
                    barThickness: 30,
                }
            }
        }
    });
</script>

<script>
    // Convert PHP data to JavaScript
    const uncleanedAddresses = JSON.parse('<?php echo json_encode($uncleanedAddresses); ?>');

    // Prepare labels and data for the chart
    const uncleanedLabels = uncleanedAddresses.map(address => address.address);
    const datasss = Array(uncleanedLabels.length).fill(1); // 1 per address to count

    const colors = Array(uncleanedLabels.length).fill('rgba(255, 99, 132, 0.9)'); // Color for uncleaned

    // Create the chart for Post Reports
    const ctxPostReport = document.getElementById('postReportsChart').getContext('2d');
    const postReportsChart = new Chart(ctxPostReport, {
        type: 'bar',
        data: {
            labels: uncleanedLabels,
            datasets: [{
                label: 'Uncleaned Addresses',
                data: datasss,
                backgroundColor: colors,
                borderColor: colors.map(color => color.replace('0.9', '1')),
                borderWidth: 1,
                barPercentage: 0.1,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count (1 per Address)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Address'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#333'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw === 1 ? '1 Address' : '';
                        }
                    }
                }
            }
        }
    });
</script>

@endpush