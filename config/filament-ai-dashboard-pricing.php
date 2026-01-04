<?php

/**
 * OpenAI model price list. Price is for 1 million tokens.
 */
return [
    'gpt-5.2' => [
        'input_tokens' => 1.75,
        'cached_tokens' => 0.125,
        'output_tokens' => 14.00
    ],
    'gpt-5.1' => [
        'input_tokens' => 1.25,
        'cached_tokens' => 0.125,
        'output_tokens' => 10.00
    ],
    'gpt-5' => [
        'input_tokens' => 1.25,
        'cached_tokens' => 0.125,
        'output_tokens' => 10.00
    ],
    'gpt-5-mini' => [
        'input_tokens' => 0.25,
        'cached_tokens' => 0.025,
        'output_tokens' => 2.00
    ],
    'gpt-5-nano' => [
        'input_tokens' => 0.05,
        'cached_tokens' => 0.005,
        'output_tokens' => 0.40
    ],
    'gpt-5.2-pro' => [
        'input_tokens' => 21.00,
        'cached_tokens' => 0,
        'output_tokens' => 168.00
    ],
    'gpt-5-pro' => [
        'input_tokens' => 15.00,
        'cached_tokens' => 0,
        'output_tokens' => 120.00
    ],
    'gpt-5-chat-latest' => [
        'input_tokens' => 1.25,
        'cached_tokens' => 0.125,
        'output_tokens' => 10.00
    ],
    'gpt-4.1' => [
        'input_tokens' => 2.00,
        'cached_tokens' => 0.50,
        'output_tokens' => 8.00
    ],
    'gpt-4.1-mini' => [
        'input_tokens' => 0.40,
        'cached_tokens' => 0.10,
        'output_tokens' => 1.60
    ],
    'gpt-4.1-nano' => [
        'input_tokens' => 0.10,
        'cached_tokens' => 0.025,
        'output_tokens' => 0.40
    ],
    'gpt-4o' => [
        'input_tokens' => 2.50,
        'cached_tokens' => 1.25,
        'output_tokens' => 10.00
    ],
    'gpt-4o-mini' => [
        'input_tokens' => 0.15,
        'cached_tokens' => 0.075,
        'output_tokens' => 0.60
    ],
    'gpt-4o-2024-08-06' => [
        'input_tokens' => 3.75,
        'cached_tokens' => 1.875,
        'output_tokens' => 15.00
    ],
    'gpt-4o-mini-2024-07-18' => [
        'input_tokens' => 0.30,
        'cached_tokens' => 0.15,
        'output_tokens' => 1.20
    ],
    'gpt-4-0125-preview' => [
        'input_tokens' => 10.00,
        'cached_tokens' => 0,
        'output_tokens' => 30.00
    ],
    'gpt-4-0613' => [
        'input_tokens' => 30.00,
        'cached_tokens' => 0,
        'output_tokens' => 60.00
    ],
    'gpt-4.5-preview' => [
        'input_tokens' => 10.00,
        'cached_tokens' => 0,
        'output_tokens' => 30.00
    ],
    'gpt-3.5-turbo-0125' => [
        'input_tokens' => 0.50,
        'cached_tokens' => 0.025,
        'output_tokens' => 1.50
    ],
    'gpt-3.5-turbo-1106' => [
        'input_tokens' => 1.00,
        'cached_tokens' => 0,
        'output_tokens' => 2.00
    ],
    'o1' => [
        'input_tokens' => 15.00,
        'cached_tokens' => 7.50,
        'output_tokens' => 60.00
    ],
    'o1-pro' => [
        'input_tokens' => 150.00,
        'cached_tokens' => 0,
        'output_tokens' => 600.00
    ],
    'o3' => [
        'input_tokens' => 2.00,
        'cached_tokens' => 0.50,
        'output_tokens' => 8.00
    ],
    'o3-pro' => [
        'input_tokens' => 20.00,
        'cached_tokens' => 0,
        'output_tokens' => 80.00
    ],
    'o3-mini' => [
        'input_tokens' => 1.10,
        'cached_tokens' => 0.55,
        'output_tokens' => 4.40
    ],
    'o3-deep-research' => [
        'input_tokens' => 10.00,
        'cached_tokens' => 2.50,
        'output_tokens' => 40.00
    ],
    'gpt-4o-mini-tts' => [
        'input_tokens' => 0.60,
        'cached_tokens' => 0,
        'output_tokens' => 0.00
    ],
    'gpt-4o-transcribe' => [
        'input_tokens' => 2.50,
        'cached_tokens' => 0,
        'output_tokens' => 10.00
    ],
    'default' => [
        'input_tokens' => 0,
        'cached_tokens' => 0,
        'output_tokens' => 0
    ],
];
