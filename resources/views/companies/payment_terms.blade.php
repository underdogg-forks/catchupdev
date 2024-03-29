@extends('header')

@section('content')
    @parent

    @include('companies.nav', ['selected' => COMPANY_PAYMENT_TERMS])

    {!! Button::primary(trans('texts.create_payment_term'))
          ->asLinkTo(URL::to('/payment_terms/create'))
          ->withAttributes(['class' => 'pull-right'])
          ->appendIcon(Icon::create('plus-sign')) !!}

    @include('partials.bulk_form', ['entityType' => ENTITY_PAYMENT_TERM])

    {!! Datatable::table()
        ->addColumn(
          trans('texts.name'),
          trans('texts.num_days'),
          trans('texts.action'))
        ->setUrl(url('api/payment_terms/'))
        ->setOptions('sPaginationType', 'bootstrap')
        ->setOptions('bFilter', false)
        ->setOptions('bAutoWidth', false)
        ->setOptions('aoColumns', [[ "sWidth"=> "40%" ], [ "sWidth"=> "40%" ], ["sWidth"=> "20%"]])
        ->setOptions('aoColumnDefs', [['bSortable'=>false, 'aTargets'=>[2]]])
        ->render('datatable') !!}

    <script>
        window.onDatatableReady = actionListHandler;
    </script>


@stop