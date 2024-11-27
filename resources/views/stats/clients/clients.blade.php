@extends('layout.default')

@section('title')
    <title>{{ __('stat.stats') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('stats') }}" class="breadcrumb__link">
            {{ __('stat.stats') }}
        </a>
    </li>
    <li class="breadcrumb--active">Clients</li>
@endsection

@section('page', 'page__stats--clients')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">Clients</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>{{ __('common.users') }}</th>
                        <th>{{ __('torrent.peers') }}</th>
                    </tr>
                </thead>
                @if (auth()->user()->group->is_modo)
                    @foreach ($clients as $prefix => $group)
                        <tbody x-data="toggle">
                            <tr x-on:click="toggle" style="cursor: pointer">
                                <td>
                                    <a
                                        href="{{ route('staff.peers.index', ['agent' => $prefix]) }}"
                                        x-on:click.stop
                                    >
                                        {{ $prefix }}
                                    </a>
                                </td>
                                <td>{{ $group['user_count'] }}</td>
                                <td>{{ $group['peer_count'] }}</td>
                            </tr>
                            @foreach ($group['clients'] as $client)
                                <tr x-cloak x-show="isToggledOn">
                                    <td style="padding: 0 0 0 24px">
                                        <a
                                            href="{{ route('staff.peers.index', ['agent' => $prefix]) }}"
                                        >
                                            {{ $client['agent'] }}
                                        </a>
                                    </td>
                                    <td>{{ $client['user_count'] }}</td>
                                    <td>{{ $client['peer_count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach
                @else
                    @foreach ($clients as $prefix => $group)
                        <tbody x-data="toggle">
                            <tr x-on:click="toggle" style="cursor: pointer">
                                <td>{{ $prefix }}</td>
                                <td>{{ $group['user_count'] }}</td>
                                <td>{{ $group['peer_count'] }}</td>
                            </tr>
                            @foreach ($group['clients'] as $client)
                                <tr x-cloak x-show="isToggledOn">
                                    <td style="padding: 0 0 0 24px">{{ $client['agent'] }}</td>
                                    <td>{{ $client['user_count'] }}</td>
                                    <td>{{ $client['peer_count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach
                @endif
            </table>
        </div>
    </section>
@endsection
