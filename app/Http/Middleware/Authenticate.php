<?php

namespace App\Http\Middleware;

use App\Models\Contact;
use App\Models\Invitation;
use Auth;
use Closure;
use Session;

/**
 * Class Authenticate.
 */
class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'user')
    {
        $authenticated = Auth::guard($guard)->check();

        if ($guard == 'client') {
            if (!empty($request->invitation_key)) {
                $contact_key = session('contact_key');
                if ($contact_key) {
                    $contact = $this->getContact($contact_key);
                    $invitation = $this->getInvitation($request->invitation_key);

                    if (!$invitation) {
                        return response()->view('error', [
                            'error' => trans('texts.invoice_not_found'),
                            'hideHeader' => true,
                        ]);
                    }

                    if ($contact && $contact->id != $invitation->contact_id) {
                        // This is a different client; reauthenticate
                        $authenticated = false;
                        Auth::guard($guard)->logout();
                    }
                    Session::put('contact_key', $invitation->contact->contact_key);
                }
            }

            if (!empty($request->contact_key)) {
                $contact_key = $request->contact_key;
                Session::put('contact_key', $contact_key);
            } else {
                $contact_key = session('contact_key');
            }

            if ($contact_key) {
                $contact = $this->getContact($contact_key);
            } elseif (!empty($request->invitation_key)) {
                $invitation = $this->getInvitation($request->invitation_key);
                $contact = $invitation->contact;
                Session::put('contact_key', $contact->contact_key);
            } else {
                return \Redirect::to('client/sessionexpired');
            }
            $company = $contact->company;

            if (Auth::guard('user')->check() && Auth::user('user')->company_id === $company->id) {
                // This is an admin; let them pretend to be a client
                $authenticated = true;
            }

            // Does this company require portal passwords?
            if ($company && (!$company->enable_portal_password || !$company->hasFeature(FEATURE_CLIENT_PORTAL_PASSWORD))) {
                $authenticated = true;
            }

            if (!$authenticated && $contact && !$contact->password) {
                $authenticated = true;
            }

            if (env('PHANTOMJS_SECRET') && $request->phantomjs_secret && hash_equals(env('PHANTOMJS_SECRET'), $request->phantomjs_secret)) {
                $authenticated = true;
            }

            if (env('PHANTOMJS_SECRET') && $request->phantomjs_secret && hash_equals(env('PHANTOMJS_SECRET'), $request->phantomjs_secret)) {
                $authenticated = true;
            }
        }

        if (!$authenticated) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest($guard == 'client' ? '/client/login' : '/login');
            }
        }

        return $next($request);
    }

    /**
     * @param $key
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    protected function getInvitation($key)
    {
        $invitation = Invitation::withTrashed()->where('invitation_key', '=', $key)->first();
        if ($invitation && !$invitation->is_deleted) {
            return $invitation;
        } else {
            return null;
        }
    }

    /**
     * @param $key
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    protected function getContact($key)
    {
        $contact = Contact::withTrashed()->where('contact_key', '=', $key)->first();
        if ($contact && !$contact->is_deleted) {
            return $contact;
        } else {
            return null;
        }
    }
}
