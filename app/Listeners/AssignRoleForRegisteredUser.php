<?php

namespace App\Listeners;

use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignRoleForRegisteredUser
{
    protected mixed $defaultRole;

    protected $user;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultRole = config('project.registered_user_role');
    }

    /**
     * Handle the event.
     *
     * @param Registered $event
     * @return void
     */
    public function handle(Registered $event): void
    {
        $this->user = $event->user;

        if (! $this->hasRole($this->defaultRole)) {
            $this->attachRole($this->defaultRole);
        }
    }

    protected function hasRole(string $name)
    {
        return $this->user->roles()->where('title', $name)->exists();
    }

    protected function attachRole(string $name): void
    {
        if ($role = Role::where('title', $name)->first()) {
            $this->user->roles()->attach($role);
        }
    }
}
