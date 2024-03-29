<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Utils;

/**
 * Class Corporation.
 */
class Corporation extends Eloquent
{
    use SoftDeletes;
    use PresentableTrait;

    protected $table = 'corporations';


    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\CompanyPresenter';

    /**
     * @var array
     */
    protected $dates = [
        'deleted_at',
        'promo_expires',
        'discount_expires',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companies()
    {
        return $this->hasMany('App\Models\Company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo('App\Models\Payment');
    }

    public function hasActivePromo()
    {
        if ($this->discount_expires) {
            return false;
        }

        return $this->promo_expires && $this->promo_expires->gte(Carbon::today());
    }

    // handle promos and discounts
    public function hasActiveDiscount(Carbon $date = null)
    {
        if (!$this->discount || !$this->discount_expires) {
            return false;
        }

        $date = $date ?: Carbon::today();

        if ($this->plan_term == PLAN_TERM_MONTHLY) {
            return $this->discount_expires->gt($date);
        } else {
            return $this->discount_expires->subMonths(11)->gt($date);
        }
    }

    public function discountedPrice($price)
    {
        if (!$this->hasActivePromo() && !$this->hasActiveDiscount()) {
            return $price;
        }

        return $price - ($price * $this->discount);
    }

    public function daysUntilPlanExpires()
    {
        if (!$this->hasActivePlan()) {
            return 0;
        }

        return Carbon::parse($this->plan_expires)->diffInDays(Carbon::today());
    }

    public function hasActivePlan()
    {
        return Carbon::parse($this->plan_expires) >= Carbon::today();
    }

    public function hasExpiredPlan($plan)
    {
        if ($this->plan != $plan) {
            return false;
        }

        return Carbon::parse($this->plan_expires) < Carbon::today();
    }

    public function hasEarnedPromo()
    {
        if (!Utils::isNinjaProd() || Utils::isPro()) {
            return false;
        }

        // if they've already been pro return false
        if ($this->plan_expires && $this->plan_expires != '0000-00-00') {
            return false;
        }

        // if they've already been pro return false
        if ($this->plan_expires && $this->plan_expires != '0000-00-00') {
            return false;
        }

        // if they've already had a discount or a promotion is active return false
        if ($this->discount_expires || $this->hasActivePromo()) {
            return false;
        }

        // after 52 weeks, offer a 50% discount for 3 days
        $discounts = [
            52 => [.5, 3],
            16 => [.5, 3],
            10 => [.25, 5],
        ];

        foreach ($discounts as $weeks => $promo) {
            list($discount, $validFor) = $promo;
            $difference = $this->created_at->diffInWeeks();
            if ($difference >= $weeks) {
                $this->discount = $discount;
                $this->promo_expires = date_create()->modify($validFor . ' days')->format('Y-m-d');
                $this->save();

                return true;
            }
        }

        return false;
    }
}
