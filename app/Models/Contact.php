<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Contact.
 */
class Contact extends EntityModel implements AuthenticatableContract, CanResetPasswordContract
{
    use SoftDeletes, Authenticatable, CanResetPassword;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_CONTACT;
    }

    /**
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'send_invoice',
    ];

    /**
     * @var string
     */
    public static $fieldFirstName = 'first_name';

    /**
     * @var string
     */
    public static $fieldLastName = 'last_name';

    /**
     * @var string
     */
    public static $fieldEmail = 'email';

    /**
     * @var string
     */
    public static $fieldPhone = 'phone';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function getPersonType()
    {
        return PERSON_CONTACT;
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return $this->getDisplayName();
    }

    /**
     * @return mixed|string
     */
    public function getDisplayName()
    {
        if ($this->getFullName()) {
            return $this->getFullName();
        } else {
            return $this->email;
        }
    }

    /**
     * @param $contact_key
     *
     * @return mixed
     */
    public function getContactKeyAttribute($contact_key)
    {
        if (empty($contact_key) && $this->id) {
            $this->contact_key = $contact_key = str_random(RANDOM_KEY_LENGTH);
            static::where('id', $this->id)->update(['contact_key' => $contact_key]);
        }

        return $contact_key;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if ($this->first_name || $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getLinkAttribute()
    {
        return \URL::to('client/dashboard/' . $this->contact_key);
    }
}
