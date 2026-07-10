<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PAY by square – cesta k xz
    |--------------------------------------------------------------------------
    |
    | Serverové generovanie QR (PDF, e-mail) potrebuje xz pre LZMA kompresiu.
    | Na Windows často stačí Git: C:\Program Files\Git\mingw64\bin\xz.exe
    |
    */
    'xz_binary' => env('XZ_BINARY_PATH'),

];
