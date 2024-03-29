<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Ninja\Repositories\UserRepository;
use App\Ninja\Transformers\UserTransformer;
use App\Services\UserService;
use Auth;

class UserApiController extends BaseAPIController
{
    protected $userService;
    protected $userRepo;

    protected $entityType = ENTITY_USER;

    public function __construct(UserService $userService, UserRepository $userRepo)
    {
        parent::__construct();

        $this->userService = $userService;
        $this->userRepo = $userRepo;
    }

    public function index()
    {
        $users = User::whereCompanyId(Auth::user()->company_id)
            ->withTrashed()
            ->orderBy('created_at', 'desc');

        return $this->listResponse($users);
    }

    /*
    public function store(CreateUserRequest $request)
    {
        return $this->save($request);
    }
    */

    public function update(UpdateUserRequest $request, $userPublicId)
    {
        $user = Auth::user();

        if ($request->action == ACTION_ARCHIVE) {
            $this->userRepo->archive($user);

            $transformer = new UserTransformer(Auth::user()->company, $request->serializer);
            $data = $this->createItem($user, $transformer, 'users');

            return $this->response($data);
        } else {
            return $this->save($request, $user);
        }
    }

    private function save($request, $user = false)
    {
        $user = $this->userRepo->save($request->input(), $user);

        $transformer = new UserTransformer(\Auth::user()->company, $request->serializer);
        $data = $this->createItem($user, $transformer, 'users');

        return $this->response($data);
    }
}
