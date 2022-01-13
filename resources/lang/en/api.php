<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during API response for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'references' => [
        'INVALID_AREA_ID' => 'Invalid area reference',
        'INVALID_CITY_ID' => 'Invalid city refrence',
        'INVALID_STATE_ID' => 'Invalid state refrence',
        'INVALID_LOCATION_ID' => 'Invalid location refrence',
        'INVALID_PREMISES_TYPE_ID' => 'Invalid premises type id',
        'INVALID_PREMISES_ID' => 'Invalid premises refrence',
    ],

    'links' => [
    ],

    'common' => [
        'NAME_REQUIRED' => 'Name cannot be blank',
        'EMAIL_REQUIRED' => 'Email cannot be blank',
        'EMAIL_INVALID' => 'Email is not valid',
        'EMAIL_SENT' => 'Email sent successfully',
        'EMAIL_IS_INACTIVE' => 'Sorry, Email is inactive',
        'OTP_REQUIRED' => 'OTP cannot be blank',
        '6_DIGITS_OTP' => 'OTP should be in 6 digits',
        'OTP_NOT_AVAILABLE' => 'Sorry, you have not requested for forgot password yet',
        'OTP_INVALID' => 'Sorry, your OTP is not valid',
        'NEW_PASSWORD_REQUIRED' => 'New Password cannot be blank',
        'NEW_PASSWORD_MIN_LENGTH_ERROR' => 'New Password must have at least eight characters',
        'NEW_PASSWORD_MUST_VALID_PASSWORD' => 'New Password must have one lowercase, one uppercase, one digit and one special character.',
        'NAME_MIN_LENGTH_ERROR' => 'Name must have at least 3 characters',
        'NAME_MAX_LENGTH_ERROR' => 'Maximum length for name is 50 characters',
        'CREATED' => 'Successfully Created',
        'UPDATED' => 'Successfully Updated',
        'DELETED' => 'Successfully Deleted',
        'ACTIVATED' => 'Successfully Activated',
        'DEACTIVATED' => 'Successfully Deactivated',
        'SHORT_NAME_REQUIRED'  => 'Short Name cannot be blank', 
        
        'ADDRESS1_REQUIRED' => 'Address1 cannot be blank',
        'ADDRESS2_REQUIRED' => 'Address2 cannot be blank',
        'ZIPCODE_REQUIRED' => 'Zipcode cannot be blank',
    ],

    'notifications' => [
        'INVALID_API_KEY' => 'Access denied, invalid api key',
        'INVALID_PLATFORM' => 'Access denied, invalid platform',
        'LOGIN_FAILED' => 'Invalid username and/or password',
        'AUTH_FAILED' => 'Access denied, invalid authorization code',
        'SESSION_EXPIRED' => 'Access denied, your session is expired',
        'ACCOUNT_DISABLED' => 'Your account is disabled, Please contact your administrator.',
        'LICENSE_EXPIRED' => 'Your company license is expired, Please contact support.',
        'INVALID_URL' => 'Invalid URL or unable to reach to the required resource',
        'OTHER_ERROR' => 'Unexpected error, please contact administrator',
        'NO_PERMISSION' => 'You have no permission.',
        'INIT_SUCCESSFULL' => 'Successfully initialized',
        'INVALID_COMPANY' => 'Invalid company reference',
    ],

    'states' => [
        'NAME_DUPLICATED' => 'State name already exists',
    ],

    'cities' => [
        'NAME_DUPLICATED' => 'City name already exists',
    ],

    'location' => [
        'NAME_DUPLICATED' => 'Location name already exists',
        'SHORT_NAME_REQUIRED'  => 'Short Name cannot be blank',
        'SHORT_NAME_LENGTH_ERROR' => "Short Name must have at least three characters",
        'SHORT_NAME_DUPLICATED' => "Location name already exists",
        'ZONE_REQUIRED'  => 'Zone cannot be blank',
        'PRIMARY_NUMBER_REQUIRED'  => 'It cannot be blank',
        'PRIMARY_NUMBER_LENGTH_ERROR'  => 'It should be a 10 digit phone number',
        'PRIMARY_NUMBER_DUPLICATED'  => 'It is already exists',
        'ALTERNATE_NUMBER_LENGTH_ERROR'  => 'Maximum length is 20 characters',
    ],

    'area' => [
        'NAME_DUPLICATED' => 'Area name already exists',
    ],

    'premise_type' => [
        'NAME_DUPLICATED' => 'Premise type name already exists',
    ],

    'premise' => [
        'NAME_DUPLICATED' => 'Location name already exists',        
        'SHORT_NAME_LENGTH_ERROR' => "Short Name must have at least three characters",
        'SHORT_NAME_DUPLICATED' => "Location name already exists",
        'ZONE_REQUIRED'  => 'Zone cannot be blank',
        'PRIMARY_NUMBER_REQUIRED'  => 'It cannot be blank',
        'PRIMARY_NUMBER_LENGTH_ERROR'  => 'It should be a 10 digit phone number',
        'PRIMARY_NUMBER_DUPLICATED'  => 'It is already exists',
        'ALTERNATE_NUMBER_LENGTH_ERROR'  => 'Maximum length is 20 characters',
    ],
];
