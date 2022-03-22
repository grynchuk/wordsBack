<?php

declare(strict_types=1);

namespace app\repositories\requestConditions;

enum ConditionTypeEnum: string {
    case IN = "IN";
    case IS = "IS";
    case EQUAL = "=";
    case NOT_EQUAL = "!=";
    case GRATER_THAN = ">";
    case LESS_THAN = "<";
    case EQUAL_OR_GRATER_THAN = ">=";
    case EQUAL_OR_LESS_THAN = "<=";
    case LIKE = "like";
}
