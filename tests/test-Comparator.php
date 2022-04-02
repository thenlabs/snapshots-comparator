<?php

use ThenLabs\SnapshotsComparator\Comparator;
use ThenLabs\SnapshotsComparator\ExpectationBuilder;

test(function () {
    $result = Comparator::compare([], []);

    $this->assertTrue($result->isSuccessful());
    $this->assertEmpty($result->getDeleted());
    $this->assertEmpty($result->getUpdated());
    $this->assertEmpty($result->getCreated());
    $this->assertEmpty($result->getUnusedExpectations());
});

test(function () {
    $expectations = new ExpectationBuilder();
    $expectations->expectCreated(['key1' => 'value1']);
    $expectations->expectDeleted(['key2' => 'value2']);
    $expectations->expectUpdated(['key3' => 'value3']);

    $result = Comparator::compare([], [], $expectations);

    $this->assertFalse($result->isSuccessful());
    $this->assertEmpty($result->getDeleted());
    $this->assertEmpty($result->getUpdated());
    $this->assertEmpty($result->getCreated());

    $expectedUnusedExpectations = [
        'CREATED' => ['key1' => 'value1'],
        'DELETED' => ['key2' => 'value2'],
        'UPDATED' => ['key3' => 'value3'],
    ];

    $this->assertEquals($expectedUnusedExpectations, $result->getUnusedExpectations());
});

testCase('CREATED', function () {
    testCase(function () {
        setUp(function () {
            $this->before = [];
            $this->after = ['db' => []];
        });

        method('checkDiff', function ($result) {
            $this->assertEmpty($result->getDeleted());
            $this->assertEmpty($result->getUpdated());
            $this->assertEquals($this->after, $result->getCreated());
        });

        test(function () {
            $result = Comparator::compare($this->before, $this->after, new ExpectationBuilder());

            $this->checkDiff($result);
            $this->assertFalse($result->isSuccessful());

            $unexpectations = [
                'CREATED' => ['db' => []],
            ];

            $this->assertEquals($unexpectations, $result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectCreated(['db' => []]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());

            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectCreated([
                'db' => function () {
                    return true;
                },
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());

            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectCreated([
                'db' => function () {
                    return false;
                },
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertFalse($result->isSuccessful());

            $unexpectations = [
                'CREATED' => ['db' => []],
            ];

            $this->assertEquals($unexpectations, $result->getUnexpectations());
        });
    });

    testCase(function () {
        setUp(function () {
            $this->before = [];
            $this->after = [
                'db' => [
                    'users' => [
                        '0' => [
                            'username' => 'user1',
                            'password' => 'user1',
                        ],
                    ],
                ],
            ];
        });

        method('checkDiff', function ($result) {
            $this->assertEquals($this->after, $result->getCreated());
            $this->assertEmpty($result->getUpdated());
            $this->assertEmpty($result->getDeleted());
        });

        test(function () {
            $result = Comparator::compare($this->before, $this->after, new ExpectationBuilder());

            $this->checkDiff($result);
            $this->assertFalse($result->isSuccessful());

            $unexpectations = [
                'CREATED' => $this->after,
            ];

            $this->assertEquals($unexpectations, $result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectCreated($this->after);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectCreated([
                'db' => function () {
                    return true;
                },
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectCreated([
                'db' => [
                    'users' => function () {
                        return true;
                    },
                ],
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectCreated([
                'db' => [
                    'users' => [
                        '0' => function () {
                            return true;
                        },
                    ],
                ],
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectCreated([
                'db' => [
                    'users' => [
                        '0' => [
                            'username' => function () {
                                return true;
                            },
                            'password' => function () {
                                return true;
                            },
                        ],
                    ],
                ],
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });
    });
});

testCase('DELETED', function () {
    testCase(function () {
        setUp(function () {
            $this->before = ['db' => []];
            $this->after = [];
        });

        method('checkDiff', function ($result) {
            $this->assertEmpty($result->getCreated());
            $this->assertEmpty($result->getUpdated());
            $this->assertEquals($this->before, $result->getDeleted());
        });

        test(function () {
            $result = Comparator::compare($this->before, $this->after, new ExpectationBuilder());

            $this->checkDiff($result);
            $this->assertFalse($result->isSuccessful());

            $unexpectations = [
                'DELETED' => ['db' => []],
            ];

            $this->assertEquals($unexpectations, $result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectDeleted(['db' => []]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());

            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectDeleted([
                'db' => function () {
                    return true;
                },
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());

            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectDeleted([
                'db' => function () {
                    return false;
                },
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertFalse($result->isSuccessful());

            $unexpectations = [
                'DELETED' => ['db' => []],
            ];

            $this->assertEquals($unexpectations, $result->getUnexpectations());
        });
    });

    testCase(function () {
        setUp(function () {
            $this->before = [
                'db' => [
                    'users' => [
                        '0' => [
                            'username' => 'user1',
                            'password' => 'user1',
                        ],
                    ],
                ],
            ];

            $this->after = [];
        });

        method('checkDiff', function ($result) {
            $this->assertEmpty($result->getCreated());
            $this->assertEmpty($result->getUpdated());
            $this->assertEquals($this->before, $result->getDeleted());
        });

        test(function () {
            $result = Comparator::compare($this->before, $this->after, new ExpectationBuilder());

            $this->checkDiff($result);
            $this->assertFalse($result->isSuccessful());

            $unexpectations = [
                'DELETED' => $this->before,
            ];

            $this->assertEquals($unexpectations, $result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectDeleted($this->before);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectDeleted([
                'db' => function () {
                    return true;
                },
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectDeleted([
                'db' => [
                    'users' => function () {
                        return true;
                    },
                ],
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectDeleted([
                'db' => [
                    'users' => [
                        '0' => function () {
                            return true;
                        },
                    ],
                ],
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectDeleted([
                'db' => [
                    'users' => [
                        '0' => [
                            'username' => function () {
                                return true;
                            },
                            'password' => function () {
                                return true;
                            },
                        ],
                    ],
                ],
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });
    });
});

testCase('UPDATED', function () {
    testCase(function () {
        setUp(function () {
            $this->before = ['val' => []];
            $this->after = ['val' => true];
        });

        method('checkDiff', function ($result) {
            $this->assertEmpty($result->getCreated());
            $this->assertEquals($this->after, $result->getUpdated());
            $this->assertEmpty($result->getDeleted());
        });

        test(function () {
            $result = Comparator::compare($this->before, $this->after, new ExpectationBuilder());

            $this->checkDiff($result);
            $this->assertFalse($result->isSuccessful());

            $unexpectations = [
                'UPDATED' => ['val' => true],
            ];

            $this->assertEquals($unexpectations, $result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectUpdated($this->after);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());

            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectUpdated([
                'val' => function () {
                    return true;
                },
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());

            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectUpdated([
                'val' => function () {
                    return false;
                },
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertFalse($result->isSuccessful());

            $unexpectations = [
                'UPDATED' => $this->after,
            ];

            $this->assertEquals($unexpectations, $result->getUnexpectations());
        });
    });

    testCase(function () {
        setUp(function () {
            $this->before = [
                'db' => [
                    'users' => [
                        '0' => [
                            'username' => 'user1',
                            'password' => 'user1',
                        ],
                    ],
                ],
            ];

            $this->after = [
                'db' => [
                    'users' => [
                        '0' => [
                            'username' => 'user2',
                            'password' => 'user2',
                        ],
                    ],
                ],
            ];
        });

        method('checkDiff', function ($result) {
            $this->assertEmpty($result->getCreated());
            $this->assertEquals($this->after, $result->getUpdated());
            $this->assertEmpty($result->getDeleted());
        });

        test(function () {
            $result = Comparator::compare($this->before, $this->after, new ExpectationBuilder());

            $this->checkDiff($result);
            $this->assertFalse($result->isSuccessful());

            $unexpectations = [
                'UPDATED' => $this->after,
            ];

            $this->assertEquals($unexpectations, $result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectUpdated($this->after);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectUpdated([
                'db' => function () {
                    return true;
                },
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectUpdated([
                'db' => [
                    'users' => function () {
                        return true;
                    },
                ],
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectUpdated([
                'db' => [
                    'users' => [
                        '0' => function () {
                            return true;
                        },
                    ],
                ],
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });

        test(function () {
            $expectations = new ExpectationBuilder();
            $expectations->expectUpdated([
                'db' => [
                    'users' => [
                        '0' => [
                            'username' => function () {
                                return true;
                            },
                            'password' => function () {
                                return true;
                            },
                        ],
                    ],
                ],
            ]);

            $result = Comparator::compare($this->before, $this->after, $expectations);

            $this->checkDiff($result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEmpty($result->getUnexpectations());
        });
    });
});
