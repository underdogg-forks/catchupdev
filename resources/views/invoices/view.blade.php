@extends('public.header')

@section('head')
    @parent

    @include('money_script')

    @foreach ($invoice->client->company->getFontFolders() as $font)
        <script src="{{ asset('js/vfs_fonts/'.$font.'.js') }}" type="text/javascript"></script>
    @endforeach

    <script src="{{ asset('pdf.built.js') }}?no_cache={{ NINJA_VERSION }}" type="text/javascript"></script>

    @if ($company->showSignature($invoice))
        <script src="{{ asset('js/jSignature.min.js') }}"></script>
    @endif

    <style type="text/css">
        body {
            background-color: #f8f8f8;
        }

        .dropdown-menu li a {
            overflow: hidden;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        #signature {
            border: 2px dotted black;
            background-color: lightgrey;
        }
    </style>

    @if (!empty($transactionToken) && $companyGateway->gateway_id == GATEWAY_BRAINTREE)
        <div id="paypal-container"></div>
        <script type="text/javascript" src="https://js.braintreegateway.com/js/braintree-2.23.0.min.js"></script>
        <script type="text/javascript">
            $(function () {
                var paypalLink = $('.dropdown-menu a[href$="paypal"]'),
                        paypalUrl = paypalLink.attr('href'),
                        checkout;
                paypalLink.parent().attr('id', 'paypal-container');
                braintree.setup("{{ $transactionToken }}", "custom", {
                    onReady: function (integration) {
                        checkout = integration;
                        $('.dropdown-menu a[href$="#braintree_paypal"]').each(function () {
                            var el = $(this);
                            el.attr('href', el.attr('href').replace('#braintree_paypal', '?device_data=' + encodeURIComponent(integration.deviceData)))
                        })
                    },
                    paypal: {
                        container: "paypal-container",
                        singleUse: false,
                        enableShippingAddress: false,
                        enableBillingAddress: false,
                        headless: true,
                        locale: "{{ $invoice->client->language ? $invoice->client->language->locale : $invoice->company->language->locale }}"
                    },
                    dataCollector: {
                        paypal: true
                    },
                    onPaymentMethodReceived: function (obj) {
                        window.location.href = paypalUrl.replace('#braintree_paypal', '') + '/' + encodeURIComponent(obj.nonce) + "?device_data=" + encodeURIComponent(JSON.stringify(obj.details));
                    }
                });
                paypalLink.click(function (e) {
                    e.preventDefault();
                    checkout.paypal.initAuthFlow();
                })
            });
        </script>
    @elseif(!empty($enableWePayACH))
        <script type="text/javascript" src="https://static.wepay.com/js/tokenization.v2.js"></script>
        <script type="text/javascript">
            $(function () {
                var achLink = $('.dropdown-menu a[href$="/bank_transfer"]'),
                        achUrl = achLink.attr('href');
                WePay.set_endpoint('{{ WEPAY_ENVIRONMENT }}');
                achLink.click(function (e) {
                    e.preventDefault();

                    $('#wepay-error').remove();
                    var email = {!! json_encode($contact->email) !!} ||
                    prompt('{{ trans('texts.ach_email_prompt') }}');
                    if (!email)return;

                    WePay.bank_company.create({
                        'client_id': '{{ WEPAY_CLIENT_ID }}',
                        'email': email
                    }, function (data) {
                        dataObj = JSON.parse(data);
                        if (dataObj.bank_company_id) {
                            window.location.href = achLink.attr('href') + '/' + dataObj.bank_company_id + "?details=" + encodeURIComponent(data);
                        } else if (dataObj.error) {
                            $('#wepay-error').remove();
                            achLink.closest('.container').prepend($('<div id="wepay-error" style="margin-top:20px" class="alert alert-danger"></div>').text(dataObj.error_description));
                        }
                    });
                });
            });
        </script>
    @endif
@stop

@section('content')

    <div class="container">

        @if (!empty($partialView))
            @include($partialView)
        @else
            <div id="paymentButtons" class="pull-right" style="text-align:right">
                @if ($invoice->isQuote())
                    {!! Button::normal(trans('texts.download_pdf'))->withAttributes(['onclick' => 'onDownloadClick()'])->large() !!}
                    &nbsp;&nbsp;
                    @if ($showApprove)
                        {!! Button::success(trans('texts.approve'))->asLinkTo(URL::to('/approve/' . $invitation->invitation_key))->large() !!}
                    @endif
                @elseif ( ! $invoice->canBePaid())
                    {!! Button::normal(trans('texts.download_pdf'))->withAttributes(['onclick' => 'onDownloadClick()'])->large() !!}
                @elseif ($invoice->client->company->isGatewayConfigured() && floatval($invoice->balance) && !$invoice->is_recurring)
                    {!! Button::normal(trans('texts.download_pdf'))->withAttributes(['onclick' => 'onDownloadClick()'])->large() !!}
                    &nbsp;&nbsp;
                    @if (count($paymentTypes) > 1)
                        {!! DropdownButton::success(trans('texts.pay_now'))->withContents($paymentTypes)->large() !!}
                    @elseif (count($paymentTypes) == 1)
                        <a href='{!! $paymentURL !!}' class="btn btn-success btn-lg">{{ trans('texts.pay_now') }}</a>
                    @endif
                @else
                    {!! Button::normal(trans('texts.download_pdf'))->withAttributes(['onclick' => 'onDownloadClick()'])->large() !!}
                    @if ($company->isNinjaCompany())
                        {!! Button::primary(trans('texts.return_to_app'))->asLinkTo(URL::to('/settings/company_management'))->large() !!}
                    @endif
                @endif
            </div>
        @endif

        <div class="pull-left">
            @if(!empty($documentsZipURL))
                {!! Button::normal(trans('texts.download_documents', array('size'=>Form::human_filesize($documentsZipSize))))->asLinkTo($documentsZipURL)->large() !!}
            @endif
        </div>

        <div class="clearfix"></div>
        <p>&nbsp;</p>
        @if ($company->isPro() && $invoice->hasDocuments())
            <div class="invoice-documents">
                <h3>{{ trans('texts.documents_header') }}</h3>
                <ul>
                    @foreach ($invoice->documents as $document)
                        <li><a target="_blank" href="{{ $document->getClientUrl($invitation) }}">{{$document->name}}
                                ({{Form::human_filesize($document->size)}})</a></li>
                    @endforeach
                    @foreach ($invoice->expenses as $expense)
                        @foreach ($expense->documents as $document)
                            <li><a target="_blank" href="{{ $document->getClientUrl($invitation) }}">{{$document->name}}
                                    ({{Form::human_filesize($document->size)}})</a></li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($company->hasFeature(FEATURE_DOCUMENTS) && $company->invoice_embed_documents)
            @foreach ($invoice->documents as $document)
                @if($document->isPDFEmbeddable())
                    <script src="{{ $document->getClientVFSJSUrl() }}" type="text/javascript" async></script>
                @endif
            @endforeach
            @foreach ($invoice->expenses as $expense)
                @foreach ($expense->documents as $document)
                    @if($document->isPDFEmbeddable())
                        <script src="{{ $document->getClientVFSJSUrl() }}" type="text/javascript" async></script>
                    @endif
                @endforeach
            @endforeach
        @endif
        <script type="text/javascript">

            window.invoice = {!! $invoice->toJson() !!};
            invoice.features = {
                customize_invoice_design:{{ $invoice->client->company->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) ? 'true' : 'false' }},
                remove_created_by:{{ $invoice->client->company->hasFeature(FEATURE_REMOVE_CREATED_BY) ? 'true' : 'false' }},
                invoice_settings:{{ $invoice->client->company->hasFeature(FEATURE_INVOICE_SETTINGS) ? 'true' : 'false' }}
            };
            invoice.is_quote = {{ $invoice->isQuote() ? 'true' : 'false' }};
            invoice.contact = {!! $contact->toJson() !!};

            function getPDFString(cb) {
                return generatePDF(invoice, invoice.invoice_design.javascript, true, cb);
            }

            if (window.hasOwnProperty('pjsc_meta')) {
                window['pjsc_meta'].remainingTasks++;
            }

            $(function () {
                @if (Input::has('phantomjs'))
                        doc = getPDFString();
                doc.getDataUrl(function (pdfString) {
                    document.write(pdfString);
                    document.close();

                    if (window.hasOwnProperty('pjsc_meta')) {
                        window['pjsc_meta'].remainingTasks--;
                    }
                });
                @else
                    refreshPDF();
                @endif

				@if ($company->requiresAuthorization($invoice))
					$('#paymentButtons a').on('click', function (e) {
                    e.preventDefault();
                    window.pendingPaymentHref = $(this).attr('href');
                    @if ($company->showSignature($invoice))
                    if (window.pendingPaymentInit) {
                        $("#signature").jSignature('reset');
                    }
                    @endif
                    @if ($company->showAcceptTerms($invoice))
                        $('#termsCheckbox').attr('checked', false);
                    @endif
                    $('#authenticationModal').modal('show');
                });

                @if ($company->showSignature($invoice))
                    $('#authenticationModal').on('shown.bs.modal', function () {
                    if (!window.pendingPaymentInit) {
                        window.pendingPaymentInit = true;
                        $("#signature").jSignature().bind('change', function (e) {
                            setModalPayNowEnabled();
                        });
                        ;
                    }
                });
                @endif
                @endif
            });

            function onDownloadClick() {
                var doc = generatePDF(invoice, invoice.invoice_design.javascript, true);
                var fileName = invoice.is_quote ? invoiceLabels.quote : invoiceLabels.invoice;
                doc.save(fileName + '-' + invoice.invoice_number + '.pdf');
            }

            function showCustomModal() {
                $('#customGatewayModal').modal('show');
            }

            function onModalPayNowClick() {
                        @if ($company->showSignature($invoice))
                var data = {
                            signature: $('#signature').jSignature('getData', 'svgbase64')[1]
                        };
                        @else
                var data = false;
                @endif
                $.ajax({
                    url: "{{ URL::to('sign/' . $invitation->invitation_key) }}",
                    type: 'PUT',
                    data: data,
                    success: function (response) {
                        redirectToPayment();
                    }
                });
            }

            function redirectToPayment() {
                $('#authenticationModal').modal('hide');
                location.href = window.pendingPaymentHref;
            }

            function setModalPayNowEnabled() {
                var disabled = false;

                @if ($company->showAcceptTerms($invoice))
                if (!$('#termsCheckbox').is(':checked')) {
                    disabled = true;
                }
                @endif

                        @if ($company->showSignature($invoice))
                if (!$('#signature').jSignature('isModified')) {
                    disabled = true;
                }
                @endif

                $('#modalPayNowButton').attr('disabled', disabled);
            }


        </script>

        @include('invoices.pdf', ['company' => $invoice->client->company])

        <p>&nbsp;</p>

    </div>


    @if (isset($customGatewayName))
        <div class="modal fade" id="customGatewayModal" tabindex="-1" role="dialog"
             aria-labelledby="customGatewayModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">{{ $customGatewayName }}</h4>
                    </div>

                    <div class="panel-body">
                        {!! nl2br(e($customGatewayText)) !!}
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">{{ trans('texts.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($company->requiresAuthorization($invoice))
        <div class="modal fade" id="authenticationModal" tabindex="-1" role="dialog"
             aria-labelledby="authenticationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">{{ trans('texts.authorization') }}</h4>
                    </div>

                    <div class="panel-body">
                        @if ($invoice->terms)
                            <div class="well" style="max-height:300px;overflow-y:scroll">
                                {!! nl2br(e($invoice->terms)) !!}
                            </div>
                        @endif
                        @if ($company->showSignature($invoice))
                            <div>
                                {{ trans('texts.sign_here') }}
                            </div>
                            <div id="signature"></div><br/>
                        @endif
                    </div>

                    <div class="modal-footer">
                        @if ($company->showAcceptTerms($invoice))
                            <div class="pull-left">
                                <label for="termsCheckbox" style="font-weight:normal">
                                    <input id="termsCheckbox" type="checkbox" onclick="setModalPayNowEnabled()"/>
                                    &nbsp;{{ trans('texts.i_agree') }}
                                </label>
                            </div>
                        @endif
                        <button id="modalPayNowButton" type="button" class="btn btn-success"
                                onclick="onModalPayNowClick()" disabled="">
                            {{ $invoice->isQuote() ? trans('texts.approve') : trans('texts.pay_now') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

@stop
