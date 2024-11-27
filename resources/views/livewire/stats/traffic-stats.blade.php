<section class="panelV2 panel--grid-item">
    <h2 class="panel__heading">{{ __('stat.total-traffic') }}</h2>
    <dl class="key-value">
        <div class="key-value__group">
            <dt>{{ __('stat.real') }} {{ __('stat.total-upload') }}</dt>
            <dd>{{ \App\Helpers\StringHelper::formatBytes($actual_upload, 2) }}</dd>
        </div>
        <div class="key-value__group">
            <dt>{{ __('stat.real') }} {{ __('stat.total-download') }}</dt>
            <dd>{{ \App\Helpers\StringHelper::formatBytes($actual_download, 2) }}</dd>
        </div>
        <div class="key-value__group">
            <dt>{{ __('stat.real') }} {{ __('stat.total-traffic') }}</dt>
            <dd>{{ \App\Helpers\StringHelper::formatBytes($actual_up_down, 2) }}</dd>
        </div>
        <div class="key-value__group">
            <dt>{{ __('stat.credited') }} {{ __('stat.total-upload') }}</dt>
            <dd>{{ \App\Helpers\StringHelper::formatBytes($credited_upload, 2) }}</dd>
        </div>
        <div class="key-value__group">
            <dt>{{ __('stat.credited') }} {{ __('stat.total-download') }}</dt>
            <dd>{{ \App\Helpers\StringHelper::formatBytes($credited_download, 2) }}</dd>
        </div>
        <div class="key-value__group">
            <dt>{{ __('stat.credited') }} {{ __('stat.total-traffic') }}</dt>
            <dd>{{ \App\Helpers\StringHelper::formatBytes($credited_up_down, 2) }}</dd>
        </div>
    </dl>
</section>
