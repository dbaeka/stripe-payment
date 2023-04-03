<?php

namespace Dbaeka\StripePayment\Enums;

enum PaymentType: string
{
    case CREDIT_CARD = 'credit_card';
    case BANK_TRANSFER = 'bank_transfer';
}
