<?php

use ThenLabs\TestSnapshots\Comparator\Comparator;
use ThenLabs\TestSnapshots\Comparator\ExpectationList;

test(function () {
    $result = Comparator::compare([], [], new ExpectationList);

    $this->assertTrue($result->isSuccessful());
});