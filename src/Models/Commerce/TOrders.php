<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TOrders extends Model
{
    protected $table = 't_orders';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'addition_data',
        'delivery_type',
        'delivery_address',
        'payment_type',
        'payment_id',
        'payment_status',
        'total_price',
        'goods_price',
        'delivery_price',
        'promocode_id',
        'comments',
        'user_id'
    ];

    protected $casts = [
        'addition_data' => 'array',
        'total_price' => 'decimal:2',
        'goods_price' => 'decimal:2',
        'delivery_price' => 'decimal:2',
        'payment_id' => 'integer',
        'promocode_id' => 'integer',
        'user_id' => 'integer',
    ];

    // Константы для delivery_type
    const DELIVERY_PICKUP = 'pickup';
    const DELIVERY_COURIER = 'courier';
    const DELIVERY_POST = 'post';

    // Константы для payment_type
    const PAYMENT_ONLINE = 'online';
    const PAYMENT_CASH = 'cash';

    // Константы для payment_status
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_CANCELED = 'canceled';

    /**
     * Товары в заказе
     */
    public function items(): HasMany
    {
        return $this->hasMany(TOrderItems::class, 'order_id');
    }

    /**
     * Промокод использованный в заказе
     */
    public function promocode(): BelongsTo
    {
        return $this->belongsTo(TPromocodes::class, 'promocode_id');
    }

    /**
     * Транзакция оплаты
     */
    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(TPaymentTransaction::class, 'payment_id');
    }

    /**
     * Пользователь который создал заказ
     */
    public function user(): ?BelongsTo
    {
        if (class_exists('App\Models\TUser')) {
            return $this->belongsTo(\App\Models\TUser::class, 'user_id');
        }
        return null;
    }
}
