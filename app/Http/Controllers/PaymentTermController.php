<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use App\Services\PaymentTermService;
use Input;
use Redirect;
use Session;
use URL;
use Utils;
use View;

class PaymentTermController extends BaseController
{
    /**
     * @var PaymentTermService
     */
    protected $paymentTermService;

    /**
     * PaymentTermController constructor.
     *
     * @param PaymentTermService $paymentTermService
     */
    public function __construct(PaymentTermService $paymentTermService)
    {
        //parent::__construct();

        $this->paymentTermService = $paymentTermService;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return Redirect::to('settings/' . COMPANY_PAYMENT_TERMS);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDatatable()
    {
        return $this->paymentTermService->getDatatable();
    }

    /**
     * @param $publicId
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($publicId)
    {
        $data = [
            'paymentTerm' => PaymentTerm::scope($publicId)->firstOrFail(),
            'method' => 'PUT',
            'url' => 'payment_terms/' . $publicId,
            'title' => trans('texts.edit_payment_term'),
        ];

        return View::make('companies.payment_term', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $data = [
            'paymentTerm' => null,
            'method' => 'POST',
            'url' => 'payment_terms',
            'title' => trans('texts.create_payment_term'),
        ];

        return View::make('companies.payment_term', $data);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        return $this->save();
    }

    /**
     * @param $publicId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($publicId)
    {
        return $this->save($publicId);
    }

    /**
     * @param bool $publicId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($publicId = false)
    {
        if ($publicId) {
            $paymentTerm = PaymentTerm::scope($publicId)->firstOrFail();
        } else {
            $paymentTerm = PaymentTerm::createNew();
        }

        $paymentTerm->name = trim(Input::get('name'));
        $paymentTerm->num_days = Utils::parseInt(Input::get('num_days'));
        $paymentTerm->save();

        $message = $publicId ? trans('texts.updated_payment_term') : trans('texts.created_payment_term');
        Session::flash('message', $message);

        return Redirect::to('settings/' . COMPANY_PAYMENT_TERMS);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulk()
    {
        $action = Input::get('bulk_action');
        $ids = Input::get('bulk_public_id');
        $count = $this->paymentTermService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_payment_term'));

        return Redirect::to('settings/' . COMPANY_PAYMENT_TERMS);
    }
}
