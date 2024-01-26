<?php

use App\Controller\ListContacts;
use App\Controller\PersistContact;
use App\Controller\PersistUser;

return [
    '/create-user' => PersistUser::class,
    '/list-contacts' => ListContacts::class,
    '/save-contact' => PersistContact::class,
];
