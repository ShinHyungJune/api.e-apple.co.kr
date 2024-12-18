<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'nickname'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $casts = [
        'level' => UserLevel::class,
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function productInquiries(): HasMany
    {
        return $this->hasMany(ProductInquiry::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function cartProductOptions(): HasMany
    {
        return $this->hasMany(CartProductOption::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')
            ->withTimestamps()
            ->withPivot('used_at'); // 중간 테이블의 추가 필드를 사용하려면
    }

    public function availableCoupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')
            ->withTimestamps()
            // 중간 테이블의 추가 필드를 사용하려면
                ->withPivot(['id', 'used_at'])
            // 중간 테이블 검색
                ->wherePivot('used_at', null) // 사용하지 않은 쿠폰
                ->wherePivot('expired_at', '>=', now()); // 만료되지 않은 쿠폰
    }


    public function pointTransactions()
    {
        return $this->hasMany(Point::class);
    }

    public function depositPoint($model)
    {
        return DB::transaction(function () use ($model) {
            list($amount, $desc) = $model->getDepositPoints();
            $balance = $this->points + $amount;
            $this->pointTransactions()->create([
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'deposit' => $amount,
                'description' => $desc,
                'balance' => $balance,
            ]);
            $this->points = $balance;
            return $this->save();
        });
    }

    public function withdrawalPoint($model)
    {
        list($amount, $desc) = $model->getWithdrawalPoints();

        if ($this->points < $amount) {
            abort(403, '포인트가 부족합니다.');
        }
        $balance = $this->points - $amount;
        $this->pointTransactions()->create([
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'withdrawal' => $amount,
            'description' => $desc,
            'balance' => $balance,
        ]);
        $this->points = $balance;
        return $this->save();
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

}
