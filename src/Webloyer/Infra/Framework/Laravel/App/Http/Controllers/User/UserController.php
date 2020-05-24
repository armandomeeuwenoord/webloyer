<?php

declare(strict_types=1);

namespace Webloyer\Infra\Framework\Laravel\App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Webloyer\Infra\Framework\Laravel\App\Http\Requests\User as UserRequest;
use Webloyer\App\Service\User\{
    CreateUserRequest,
    CreateUserService,
    DeleteUserRequest,
    DeleteUserService,
    GetUserRequest,
    GetUserService,
    GetUsersRequest,
    GetUsersService,
    RegenerateApiTokenRequest,
    RegenerateApiTokenService,
    UpdatePasswordRequest,
    UpdatePasswordService,
    UpdateUserRequest,
    UpdateUserService,
};
use Webloyer\Domain\Model\User as UserDomainModel;

class UserController extends Controller
{
    /** @var RoleInterface */
    private $role;

    /**
     * Create a new controller instance.
     *
     * @param \App\Repositories\Role\RoleInterface $role
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('acl');
    }

    /**
     * Display a listing of the resource.
     *
     * @param UserRequest\IndexRequest $request
     * @return Response
     */
    public function index(UserRequest\IndexRequest $request, GetUsersService $service)
    {
        $page = $request->input('page', 1);
        $perPage = 10;

        $serviceRequest = (new GetUsersRequest())
            ->setPage($page)
            ->setPerPage($perPage);
        $users = $service->execute($serviceRequest);

        return view('webloyer::users.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = $this->role->all();

        return view('webloyer::users.create')
            ->with('roles', $roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest\StoreRequest $request
     * @return Response
     */
    public function store(UserRequest\StoreRequest $request, CreateUserService $service)
    {
        $input = $request->all();

        $serviceRequest = (new CreateUserRequest())
            ->setEmail($input['email'])
            ->setName($input['name'])
            ->setPassword('password')
            ->setApiToken(Str::random(60));
        $service->execute($serviceRequest);

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param UserDomainModel\User $user
     * @return Response
     */
    public function show(UserDomainModel\User $user)
    {
        return redirect()->route('users.edit', [$user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UserDomainModel\User $user
     * @return Response
     */
    public function edit(UserDomainMode\User $user)
    {
        return view('webloyer::users.edit')->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest\UpdateRequest $request
     * @param UserDomainModel\User      $user
     * @return Response
     */
    public function update(UserRequest\UpdateRequest $request, UserDomainModel\User $user, UpdateUserService $service)
    {
        $serviceRequest = (new UpdateUserService())
//            ->setEmail($input['email'])
            ->setEmail($user->email())
            ->setName($input['name']);
        $service->execute($serviceRequest);

        return redirect()->route('users.index');
    }

    /**
     * Show the form for changing the password of the specified resource.
     *
     * @param UserDomainModel\User $user
     * @return Response
     */
    public function changePassword(UserDomainMode\User $user)
    {
        return view('webloyer::users.change_password')->with('user', $user);
    }

    /**
     * Update the password of the specified resource in storage.
     *
     * @param UserRequest\UpdatePasswordRequest $request
     * @param UserDomainModel\User              $user
     * @return Response
     */
    public function updatePassword(UserRequest\UpdatePasswordRequest $request, UserDomainModel\User $user, UpdatePasswordService $service)
    {
        $serviceRequest = (new UpdatePasswordRequest())
            ->setEmail($user->email())
            ->setPassword($input['name']);
        $service->execute($serviceRequest);

        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the role of the specified resource.
     *
     * @param UserDomainModel\User $user
     * @return Response
     */
    public function editRole(UserDomainModel\User $user)
    {
        $roles = $this->role->all();

        return view('webloyer::users.edit_role')
            ->with('user', $user)
            ->with('roles', $roles);
    }

    /**
     * Update the role of the specified resource in storage.
     *
     * @param UserRequest\UpdateRoleRequest $request
     * @param UserDomainModel\User          $user
     * @return Response
     */
    public function updateRole(UserRequest\UpdateRoleRequest $request, UserDomainModel\User $user)
    {
        $input = array_merge($request->all(), ['id' => $user->id]);

        $this->userService->updateRole($input);

        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the API token of the specified resource.
     *
     * @param UserDomainModel\User $user
     * @return Response
     */
    public function editApiToken(UserDomainModel\User $user)
    {
        return view('webloyer::users.edit_api_token')
            ->with('user', $user);
    }

    /**
     * Regenerate the API token of the specified resource in storage.
     *
     * @param UserRequest\RegenerateApiTokenRequest $request
     * @param UserDomainModel\User                  $user
     * @return Response
     */
    public function regenerateApiToken(UserRequest\RegenerateApiTokenRequest $request, UserDomainModel\User $user, RegenerateApiTokenService $service)
    {
        $serviceRequest = (new RegenerateApiTokenRequest())
            ->setEmail($user->email())
            ->setApiToken(Str::random(60));
        $service->execute($serviceRequest);

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param UserDomainModel\User $user
     * @return Response
     */
    public function destroy(UserDomainModel\User $user, DeleteUserService $service)
    {
        $serviceRequest = (new DeleteUserRequest())
            ->setEmail($user->email());
        $service->execute($serviceRequest);

        return redirect()->route('users.index');
    }
}