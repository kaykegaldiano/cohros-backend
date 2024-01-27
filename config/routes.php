<?php

use App\Controller\ListContacts;
use App\Controller\LoginUser;
use App\Controller\PersistContact;
use App\Controller\PersistUser;

return [
    '/create-user' => PersistUser::class,
    '/login-user' => LoginUser::class,
    '/list-contacts' => ListContacts::class,
    '/save-contact' => PersistContact::class,
];
