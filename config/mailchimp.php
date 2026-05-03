<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mailchimp API Key
    |--------------------------------------------------------------------------
    |
    | Your Mailchimp API key. You can generate this from your Mailchimp account
    | under Account > Extras > API keys
    |
    */

    'api_key' => env('MAILCHIMP_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Mailchimp Server Prefix
    |--------------------------------------------------------------------------
    |
    | The server prefix from your Mailchimp API key. Usually something like 'us1', 'us2', etc.
    | This is the part after the dash in your API key.
    |
    */

    'server_prefix' => env('MAILCHIMP_SERVER_PREFIX', 'us1'),

    /*
    |--------------------------------------------------------------------------
    | Mailchimp List ID
    |--------------------------------------------------------------------------
    |
    | The audience (list) ID where subscribers will be added.
    | You can find this in your Mailchimp account under Audience > Settings > Audience name and defaults
    |
    */

    'list_id' => env('MAILCHIMP_LIST_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | From Email
    |--------------------------------------------------------------------------
    |
    | The email address that will be used as the "from" address in campaigns
    |
    */

    'from_email' => env('MAILCHIMP_FROM_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@example.com')),

    /*
    |--------------------------------------------------------------------------
    | From Name
    |--------------------------------------------------------------------------
    |
    | The name that will be used as the "from" name in campaigns
    |
    */

    'from_name' => env('MAILCHIMP_FROM_NAME', env('MAIL_FROM_NAME', 'AyuMart')),

    /*
    |--------------------------------------------------------------------------
    | Reply To Email
    |--------------------------------------------------------------------------
    |
    | The email address for replies
    |
    */

    'reply_to' => env('MAILCHIMP_REPLY_TO', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
];
