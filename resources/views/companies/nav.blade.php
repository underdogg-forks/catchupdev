@if (!Utils::isPro() && isset($advanced) && $advanced)
    <div class="alert alert-warning" style="font-size:larger;">
        <center>
            {!! trans('texts.pro_plan_advanced_settings', ['link'=>'<a href="javascript:showUpgradeModal()">' . trans('texts.pro_plan_remove_logo_link') . '</a>']) !!}
        </center>
    </div>
@endif

<div class="row">

    <div class="col-md-3">
        @foreach([
            BASIC_SETTINGS => \App\Models\Company::$basicSettings,
            ADVANCED_SETTINGS => \App\Models\Company::$advancedSettings,
        ] as $type => $settings)
            <div class="panel panel-default">
                <div class="panel-heading" style="color:white">
                    {{ trans("texts.{$type}") }}
                    @if ($type === ADVANCED_SETTINGS && ! Utils::isPaidPro())
                        <sup>{{ strtoupper(trans('texts.pro')) }}</sup>
                    @endif
                </div>
                <div class="list-group">
                    @foreach ($settings as $section)
                        <a href="{{ URL::to("settings/{$section}") }}"
                           class="list-group-item {{ $selected === $section ? 'selected' : '' }}"
                           style="width:100%;text-align:left">{{ trans("texts.{$section}") }}</a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="col-md-9">
