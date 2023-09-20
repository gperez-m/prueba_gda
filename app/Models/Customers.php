<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static $status = ['ACTIVE' => 'A', 'INACTIVE' => 'I', 'DELETED' => 'trash'];

    protected $fillable = ['dni', 'id_reg', 'id_com', 'email', 'name', 'last_name', 'address', 'data_reg', 'status'];

    public function communes() {
        return $this->belongsTo(Communes::class, 'id_com', 'id_com');
    }

    public function regions() {
        return $this->belongsTo(Regions::class, 'id_reg', 'id_reg');
    }
}
