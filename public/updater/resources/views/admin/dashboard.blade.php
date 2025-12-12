@extends('admin.layout')

@php
    $admin = Auth::guard('admin')->user();
    if (!empty($admin->role)) {
        $permissions = $admin->role->permissions;
        $permissions = json_decode($permissions, true);
    }
    //Calculate Token Statistics
    $totalTokensAllocated = \App\Models\Membership::where('status', 1)->sum('total_tokens');
    $totalTokensUsed = \App\Models\Membership::where('status', 1)->sum('used_tokens');
    $totalTokensRemaining = $totalTokensAllocated - $totalTokensUsed;
    $tokenUsagePercentage = $totalTokensAllocated > 0 ? round(($totalTokensUsed / $totalTokensAllocated) * 100, 2) : 0;
@endphp

@section('content')
    <div class="mt-2 mb-4">
        <h2 class=" card-title pb-2">{{ __('Welcome back') }}, {{ Auth::guard('admin')->user()->first_name }}
            {{ Auth::guard('admin')->user()->last_name }}!</h2>
    </div>
    <div class="row">
        @if (empty($admin->role) || (!empty($permissions) && in_array('Registered Users', $permissions)))
            <div class="col-sm-6 col-md-4">
                <a class="card card-stats card-info card-round" href="{{ route('admin.register.user') }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Registered Users') }}</p>
                                    <h4 class="card-title">{{ App\Models\User::count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif


        @if (empty($admin->role) || (!empty($permissions) && in_array('Subscribers', $permissions)))
            <div class="col-sm-6 col-md-4">
                <a class="card card-stats card-warning card-round" href="{{ route('admin.subscriber.index') }}">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-mail-bulk"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Subscribers') }}</p>
                                    <h4 class="card-title">{{ App\Models\Subscriber::count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif


        @if (empty($admin->role) || (!empty($permissions) && in_array('Packages', $permissions)))
            <div class="col-sm-6 col-md-4">
                <a class="card card-stats card-success card-round" href="{{ route('admin.package.index') }}">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-list-ul"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Packages') }}</p>
                                    <h4 class="card-title">{{ App\Models\Package::count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        @if (empty($admin->role) || (!empty($permissions) && in_array('Payment Log', $permissions)))
            <div class="col-sm-6 col-md-4">
                <a class="card card-stats card-danger card-round" href="{{ route('admin.payment-log.index') }}">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-money-check-alt"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Payment Logs') }}</p>
                                    <h4 class="card-title">{{ App\Models\Membership::count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif


        @if (empty($admin->role) || (!empty($permissions) && in_array('Admins Management', $permissions)))
            <div class="col-sm-6 col-md-4">
                <a class="card card-stats card-secondary card-round" href="{{ route('admin.user.index') }}">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Admins') }}</p>
                                    <h4 class="card-title">{{ App\Models\Admin::count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        @if (empty($admin->role) || (!empty($permissions) && in_array('Blogs', $permissions)))
            <div class="col-sm-6 col-md-4">
                <a class="card card-stats card-primary card-round"
                    href="{{ route('admin.blog.index', ['language' => $defaultLang->code]) }}">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Blog') }}</p>
                                    <h4 class="card-title">{{ $defaultLang->blogs()->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif
        {{-- Required AI Tokens Box --}}
        @php
            $requiredTooltipText = '<strong>' . __('Required AI Tokens') . "</strong><br> • " . __('This shows the total number of AI tokens you need to purchase to cover all active tenants') .'. '. "<br> • " . __('It is calculated as: AI tokens per pricing plan × number of active subscriptions for that plan (summed for all plans)').'. ' ."<br>
        • " .  __('This is the allocated token requirement, not the tokens already used') . '.';
        @endphp


        <div class="col-sm-6 col-md-4">
            {{-- <a class="card card-stats card-info card-round" href="{{ route('admin.ai-tokens.index') }}"> --}}
            <a class="card card-stats card-info card-round position-relative card-tooltip-trigger" href="javaScript:void(0)"
                data-toggle="tooltip" data-placement="top" data-html="true" title="{!! $requiredTooltipText !!}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-robot"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">
                                    {{ __('Required AI Tokens') . ' (' . __('All Tenants') . ')' }}
                                </p>
                                <h4 class="card-title">{{ number_format($totalTokensAllocated) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Remaining AI Tokens Box --}}

        @php
            $remainingTooltipText =  '<strong>' . __('Remaining AI Tokens') . "</strong><br>• " . __('This shows how many AI tokens are still available for all tenants after deducting the used tokens from the total allocated tokens' ) .'. '.
                "<br>• " . __('Remaining Tokens = Required Tokens − Used Tokens') . "<br>• " . __('This indicates the unused token balance that is still safe to use') . '.';
        @endphp


        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-success card-round card-tooltip-trigger" data-toggle="tooltip"
                data-placement="top" data-html="true" title="{!! $remainingTooltipText !!}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-robot"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">
                                    {{ __('Remaining AI Tokens') }}
                                </p>
                                <h4 class="card-title">{{ number_format($totalTokensRemaining) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Total Tokens Used Box --}}
        @php
            $usedTooltipText =
                '<strong>' . __('Used AI Tokens') .  "</strong><br> • " .  __('This shows how many AI tokens have already been consumed by all tenants who are subscribed to AI content generation plans' ) . '. ' .   "<br> • " .    __('It includes token usage from all AI-enabled pricing plans and all their users/tenants') . '. ' .  "<br>  • " .  __('This is the total used tokens, not the remaining or required tokens') .'. ' .    '';
        @endphp


        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-warning card-round card-tooltip-trigger" data-toggle="tooltip"
                data-placement="top" data-html="true" title="{!! $usedTooltipText !!}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ __('Used AI Tokens') . ' (' . __('All Tenants') . ')' }}</p>
                                <h4 class="card-title">{{ number_format($totalTokensUsed) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @if (empty($admin->role) || (!empty($permissions) && in_array('Payment Log', $permissions)))
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">{{ __('Monthly Income') }} ({{ date('Y') }})</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (empty($admin->role) || (!empty($permissions) && in_array('Registered Users', $permissions)))
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">{{ __('Monthly Premium Users') }} ({{ date('Y') }})</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="usersChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@php
    $months = [];
    $inTotals = [];

    for ($i = 1; $i <= 12; $i++) {
        $monthNum = $i;
        $dateObj = DateTime::createFromFormat('!m', $monthNum);
        $months[] = $dateObj->format('M');

        $inFound = 0;
        foreach ($incomes as $key => $income) {
            if ($income->month == $i) {
                $inTotals[] = $income->total;
                $inFound = 1;
                break;
            }
        }
        if ($inFound == 0) {
            $inTotals[] = 0;
        }

        $userFound = 0;
        foreach ($users as $key => $user) {
            if ($user->month == $i) {
                $userTotals[] = $user->total;
                $userFound = 1;
                break;
            }
        }
        if ($userFound == 0) {
            $userTotals[] = 0;
        }
    }

@endphp
@section('scripts')
    <!-- Chart JS -->
    <script src="{{ asset('assets/admin/js/plugin/chart.min.js') }}"></script>
    <script>
        "use strict";
        var months = @json($months);
        var inTotals = @json($inTotals);
        var userTotals = @json($userTotals);
    </script>
    <script src="{{ asset('assets/admin/js/dashboard.js') }}"></script>
@endsection
