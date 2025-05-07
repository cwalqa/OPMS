<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class QuickbooksCustomer extends Authenticatable
{
    use HasFactory, Notifiable;

    // Specify the table name
    protected $table = 'quickbooks_customer';

    // Mass assignable attributes
    protected $guarded = [];

    // Hidden attributes for arrays
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Casts for attributes
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Insert or update customers in the database.
     *
     * @param array $insert_customer_data
     * @return mixed
     */
    public static function customerInsertUpdate($insert_customer_data)
    {
        // Get all existing data
        $data = self::get();

        if (count($data) > 0) {
            // Update or create customers
            foreach ($insert_customer_data as $key => $customer_details) {
                $result = self::updateOrCreate(
                    [
                        'customer_id' => $customer_details['customer_id'],
                    ],
                    $customer_details
                );
            }
        } else {
            // Insert new customer data
            $result = self::insert($insert_customer_data);
        }

        return $result;
    }

    public function resetTwoFactorCode()
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }
}
