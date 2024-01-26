<?php

use App\Controller\ListContacts;
use App\Controller\PersistContact;

return [
    '/list-contacts' => ListContacts::class,
    '/save-contact' => PersistContact::class
];
