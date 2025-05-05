<?php

namespace App\Models;

use App\Enums\BasicAccountsSetupEnum;
use Illuminate\Database\Eloquent\Model;

class Setup extends Model
{
    protected $fillable = ['name', 'type', 'account_id'];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public static function getAccountId(BasicAccountsSetupEnum $name)
    {
        $cacheKey = 'account_id_' . $name->value;

        return cache()->remember($cacheKey, now()->addDay(), function () use ($name) {
            $account = static::where('name', $name->value)->first();

            if (!$account) {
                // Fallback to config if available
                $fallback = config("accounts.fallbacks.{$name->value}");

                if ($fallback) {
                    return $fallback;
                }

                throw new \RuntimeException(
                    "No account configured for {$name->value} and no fallback exists"
                );
            }

            return $account->account_id;
        });
    }
}
