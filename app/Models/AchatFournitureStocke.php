<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchatFournitureStocke extends Model
{
    use HasFactory;
    protected $fillable = ['status','setOfBooksId','accountingDate','currencyCode','dateCreated','createdBy','net_a_payer_engage','actualFlag','userJeCategoryName','userJeSourceName','segment1','segment2','segment3','segment4','segment5','segment6','segment7','segment8','reference4','reference5','reference6','reference7','reference10','enteredDr','enteredCr','ledgerId','encumbranceTypeId','flag_actif'];

}
