@extends('admin.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">
            <i class="fas fa-robot"></i> {{ __('AI Token Management') }}
        </h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('AI Tokens') }}</a>
            </li>
        </ul>
    </div>

    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-primary card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ __('Total User Purchased Tokens') }}</p>
                                <h4 class="card-title">{{ number_format($totalTokensAllocated) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-success card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ __('Remaining User Tokens') }}</p>
                                <h4 class="card-title">{{ number_format($totalTokensRemaining) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-warning card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ __('Total Tokens Used') }}</p>
                                <h4 class="card-title">{{ number_format($totalTokensUsed) }}</h4>
                                <small class="">{{ $tokenUsagePercentage }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-info card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ __('Active Users') }}</p>
                                <h4 class="card-title">{{ $topConsumers->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Token Distribution Overview --}}
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-chart-bar"></i> {{ __('Token Distribution') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 40px;">
                        <div class="progress-bar bg-success" 
                             role="progressbar" 
                             style="width: {{ $totalTokensAllocated > 0 ? (($totalTokensRemaining / $totalTokensAllocated) * 100) : 0 }}%">
                            <strong>{{ __('Available') }}: {{ number_format($totalTokensRemaining) }}</strong>
                        </div>
                        <div class="progress-bar bg-warning" 
                             role="progressbar" 
                             style="width: {{ $tokenUsagePercentage }}%">
                            <strong>{{ __('Used') }}: {{ number_format($totalTokensUsed) }}</strong>
                        </div>
                    </div>

                    <h5 class="mt-4">{{ __('Usage by Action') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Action') }}</th>
                                    <th class="text-right">{{ __('Total Tokens') }}</th>
                                    <th class="text-right">{{ __('Count') }}</th>
                                    <th class="text-right">{{ __('Avg per Use') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usageByAction as $item)
                                    <tr>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ ucwords(str_replace('_', ' ', $item->action)) }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ number_format($item->total) }}</td>
                                        <td class="text-right">{{ number_format($item->count) }}</td>
                                        <td class="text-right">
                                            {{ number_format($item->total / $item->count) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-trophy"></i> {{ __('Top Consumers') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($topConsumers as $index => $consumer)
                            <a href="javascript:void(0)" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <span class="badge badge-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                            #{{ $index + 1 }}
                                        </span>
                                        <strong>{{ $consumer->user->username ?? 'N/A' }}</strong>
                                    </div>
                                    <span class="text-primary">
                                        {{ number_format($consumer->total_used) }} {{ __('tokens') }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                {{ __('No data available') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-history"></i> {{ __('Recent Token Activity') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Action') }}</th>
                                    <th class="text-right">{{ __('Tokens Used') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Details') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsage as $usage)
                                    <tr>
                                        <td>
                                            <a href="{{ route('register.user.view', $usage->user_id) }}">
                                                {{ $usage->user->username ?? 'Unknown' }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ ucwords(str_replace('_', ' ', $usage->action)) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ number_format($usage->tokens_used) }}</strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $usage->created_at->format('M d, Y H:i') }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                {{ $usage->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($usage->details)
                                                <button class="btn btn-sm btn-info" 
                                                        data-toggle="modal" 
                                                        data-target="#detailsModal{{ $usage->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                          
                                                <div class="modal fade" id="detailsModal{{ $usage->id }}">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('Usage Details') }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <pre class="json-pretty">{{ json_encode(json_decode($usage->details), JSON_PRETTY_PRINT) }}</pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            {{ __('No token usage recorded yet') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $recentUsage->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
