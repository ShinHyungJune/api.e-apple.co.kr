<?php

namespace App\Policies;

use App\Models\Post\Post;
use App\Models\User;

class PostPolicy
{
    public function before(User $user, $ability)
    {
        //TODO
        /*if ($user->isAdmin()) {
            return true;
        }*/
    }


    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user, $boardType): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user, $board): bool
    {
        if ($board->level_create === 0) return true;
        return $user && $user?->role <= $board->level_create;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(?User $user, Post $post): bool
    {
        return $user && $user->id === $post->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?User $user, Post $post): bool
    {
        return $user && $user->id === $post->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        //
    }
}
