<?php

namespace App\Policies;

use App\Models\CallLog;
use App\Models\User;

class CallLogPolicy
{
    /**
     * ユーザーが架電記録を一覧表示できるかを判定
     *
     * @param  User  $user  対象ユーザー
     * @return bool 認証済みユーザーは一覧表示可能
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * ユーザーが指定された架電記録を閲覧できるかを判定
     *
     * @param  User  $user  対象ユーザー
     * @param  CallLog  $callLog  対象架電記録
     * @return bool 自分の架電記録のみ閲覧可能
     */
    public function view(User $user, CallLog $callLog): bool
    {
        return $user->id === $callLog->user_id;
    }

    /**
     * ユーザーが架電記録を作成できるかを判定
     *
     * @param  User  $user  対象ユーザー
     * @return bool 認証済みユーザーは作成可能
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * ユーザーが指定された架電記録を更新できるかを判定
     *
     * @param  User  $user  対象ユーザー
     * @param  CallLog  $callLog  対象架電記録
     * @return bool 自分の架電記録のみ更新可能
     */
    public function update(User $user, CallLog $callLog): bool
    {
        return $user->id === $callLog->user_id;
    }

    /**
     * ユーザーが指定された架電記録を削除できるかを判定
     *
     * @param  User  $user  対象ユーザー
     * @param  CallLog  $callLog  対象架電記録
     * @return bool 自分の架電記録のみ削除可能
     */
    public function delete(User $user, CallLog $callLog): bool
    {
        return $user->id === $callLog->user_id;
    }

    /**
     * ユーザーが指定された架電記録を復元できるかを判定
     *
     * @param  User  $user  対象ユーザー
     * @param  CallLog  $callLog  対象架電記録
     * @return bool 自分の架電記録のみ復元可能
     */
    public function restore(User $user, CallLog $callLog): bool
    {
        return $user->id === $callLog->user_id;
    }

    /**
     * ユーザーが指定された架電記録を完全削除できるかを判定
     *
     * @param  User  $user  対象ユーザー
     * @param  CallLog  $callLog  対象架電記録
     * @return bool 自分の架電記録のみ完全削除可能
     */
    public function forceDelete(User $user, CallLog $callLog): bool
    {
        return $user->id === $callLog->user_id;
    }
}
