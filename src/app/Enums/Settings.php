<?php

declare(strict_types=1);

namespace App\Enums;

enum Settings: string
{
    case EDIT_CUSTOMER_ID = 'allow_edit_customer_id';
    case EMAIL_SENDER_ADDRESS = 'email_sender_address';
}
