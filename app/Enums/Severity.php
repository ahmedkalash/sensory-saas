<?php

namespace App\Enums;

enum Severity: string
{
    case OK = 'لا يوجد اضراب';
    case LOW = 'بسيط';
    case MID = 'متوسط';
    case HIGH = 'شديد';
}
