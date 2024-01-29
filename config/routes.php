<?php

use App\Controller\ListContact;
use App\Controller\ListContacts;
use App\Controller\PersistContact;
use App\Controller\PersistUser;
use App\Controller\RemoveContact;

return [
    '/create-user' => PersistUser::class,
    '/list-contacts' => ListContacts::class,
    '/list-contact' => ListContact::class,
    '/save-contact' => PersistContact::class,
    '/remove-contact' => RemoveContact::class,
];
