<?php

namespace App\Entity;

enum State: string
{
    case Created = "CREATED";
    case Open = "OPEN";
    case Closed = "CLOSED";
    case InProgress = "IN_PROGRESS";
    case Passed = "PASSED";
    case Canceled = "CANCELED";
}
