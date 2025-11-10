<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * ユーザーが顧客一覧を閲覧できるかを判定する
     *
     * @param  User  $user  対象ユーザー
     * @return bool 閲覧可能な場合true
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * ユーザーが指定の顧客を閲覧できるかを判定する
     *
     * @param  User  $user  対象ユーザー
     * @param  Customer  $customer  対象顧客
     * @return bool 自分の顧客の場合true
     */
    public function view(User $user, Customer $customer): bool
    {
        return $customer->user_id === $user->id;
    }

    /**
     * ユーザーが顧客を新規作成できるかを判定する
     *
     * @param  User  $user  対象ユーザー
     * @return bool 作成可能な場合true
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * ユーザーが指定の顧客を更新できるかを判定する
     *
     * @param  User  $user  対象ユーザー
     * @param  Customer  $customer  対象顧客
     * @return bool 自分の顧客の場合true
     */
    public function update(User $user, Customer $customer): bool
    {
        return $customer->user_id === $user->id;
    }

    /**
     * ユーザーが指定の顧客を削除できるかを判定する
     *
     * @param  User  $user  対象ユーザー
     * @param  Customer  $customer  対象顧客
     * @return bool 自分の顧客の場合true
     */
    public function delete(User $user, Customer $customer): bool
    {
        return $customer->user_id === $user->id;
    }

    /**
     * ユーザーが指定の顧客を復元できるかを判定する
     *
     * @param  User  $user  対象ユーザー
     * @param  Customer  $customer  対象顧客
     * @return bool 自分の顧客の場合true
     */
    public function restore(User $user, Customer $customer): bool
    {
        return $customer->user_id === $user->id;
    }

    /**
     * ユーザーが指定の顧客を物理削除できるかを判定する
     *
     * @param  User  $user  対象ユーザー
     * @param  Customer  $customer  対象顧客
     * @return bool 自分の顧客の場合true
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        return $customer->user_id === $user->id;
    }
}
