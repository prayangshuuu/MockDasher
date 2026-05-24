<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'gemini' => [
        'key'   => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    ],

    'ielts' => [
        'speaking_part1_context' => env('IELTS_SPEAKING_PART1_CONTEXT', 'IELTS Speaking Part 1: The examiner asks about familiar topics.'),
        'speaking_part2_context' => env('IELTS_SPEAKING_PART2_CONTEXT', 'IELTS Speaking Part 2: The candidate speaks for 1-2 minutes on a topic.'),
        'speaking_part3_context' => env('IELTS_SPEAKING_PART3_CONTEXT', 'IELTS Speaking Part 3: The examiner and candidate discuss abstract topics.'),
        'writing_task1_context'  => env('IELTS_WRITING_TASK1_CONTEXT', 'IELTS Writing Task 1: Summarise the graph/chart in at least 150 words.'),
        'writing_task2_context'  => env('IELTS_WRITING_TASK2_CONTEXT', 'IELTS Writing Task 2: Write an essay of at least 250 words.'),
    ],

];
