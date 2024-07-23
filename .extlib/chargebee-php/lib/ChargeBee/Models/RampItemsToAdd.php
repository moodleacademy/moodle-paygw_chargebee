<?php

namespace ChargeBee\ChargeBee\Models;

use ChargeBee\ChargeBee\Model;

class RampItemsToAdd extends Model
{
  protected $allowed = [
    'itemPriceId',
    'itemType',
    'quantity',
    'quantityInDecimal',
    'unitPrice',
    'unitPriceInDecimal',
    'amount',
    'amountInDecimal',
    'freeQuantity',
    'freeQuantityInDecimal',
    'billingCycles',
    'servicePeriodDays',
    'meteredQuantity',
  ];

}

?>