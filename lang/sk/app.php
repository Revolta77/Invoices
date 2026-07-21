<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Všeobecné
    |--------------------------------------------------------------------------
    */

    'toggle_dark_mode' => 'Prepnúť tmavý režim',
    'switch_language' => 'Zmeniť jazyk',
    'tagline' => 'Správa faktúr jednoducho a prehľadne',

    'or' => 'alebo',
    'dash' => '—',
    'currency_default' => 'EUR',
    'unit_default' => 'ks',

    /*
    |--------------------------------------------------------------------------
    | Autentifikácia
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'login' => [
            'title' => 'Prihlásenie',
            'subtitle' => 'Prihláste sa do svojho účtu',
            'email' => 'E-mail',
            'password' => 'Heslo',
            'remember' => 'Zapamätať si ma',
            'submit' => 'Prihlásiť sa',
            'submitting' => 'Prihlasujem...',
            'google' => 'Prihlásiť sa cez Google',
            'no_account' => 'Nemáte účet?',
            'register_link' => 'Zaregistrujte sa',
            'forgot_password' => 'Zabudnuté heslo?',
        ],
        'register' => [
            'title' => 'Registrácia',
            'subtitle' => 'Vytvorte si nový účet',
            'name' => 'Meno',
            'email' => 'E-mail',
            'password' => 'Heslo',
            'password_confirmation' => 'Potvrdenie hesla',
            'submit' => 'Zaregistrovať sa',
            'submitting' => 'Registrujem...',
            'google' => 'Registrovať sa cez Google',
            'has_account' => 'Už máte účet?',
            'login_link' => 'Prihláste sa',
        ],
        'forgot_password' => [
            'title' => 'Obnovenie hesla',
            'subtitle' => 'Zadajte e-mail a pošleme vám odkaz na nastavenie nového hesla.',
            'email' => 'E-mail',
            'submit' => 'Odoslať odkaz',
            'submitting' => 'Odosielam...',
            'sent' => 'Ak účet s týmto e-mailom existuje, odkaz na obnovenie hesla bol odoslaný.',
            'back_to_login' => 'Späť na prihlásenie',
        ],
        'reset_password' => [
            'title' => 'Nové heslo',
            'subtitle' => 'Zadajte nové heslo pre svoj účet.',
            'email' => 'E-mail',
            'password' => 'Nové heslo',
            'password_confirmation' => 'Potvrdenie hesla',
            'submit' => 'Nastaviť heslo',
            'submitting' => 'Ukladám...',
            'success' => 'Heslo bolo úspešne zmenené. Môžete sa prihlásiť.',
            'back_to_login' => 'Späť na prihlásenie',
        ],
        'verification' => [
            'modal_title' => 'Overte svoj e-mail',
            'modal_description' => 'Účet :email ešte nie je aktivovaný. Skontrolujte doručenú poštu a kliknite na aktivačný odkaz.',
            'expires_hint' => 'Aktivačný odkaz je platný :minutes minút.',
            'resend' => 'Poslať znova',
            'resending' => 'Odosielam...',
            'resend_wait' => 'Poslať znova o :seconds s',
            'resent' => 'Aktivačný e-mail bol odoslaný.',
            'close' => 'Zavrieť',
            'verified_success' => 'E-mail bol overený. Teraz sa môžete prihlásiť.',
            'already_verified' => 'E-mail je už overený. Môžete sa prihlásiť.',
            'link_invalid' => 'Aktivačný odkaz je neplatný.',
            'link_expired' => 'Aktivačný odkaz vypršal. Prihláste sa a požiadajte o nový.',
        ],
    ],

    'emails' => [
        'welcome' => [
            'subject' => 'Vitajte v :app',
            'heading' => 'Vitajte, :name!',
            'intro' => 'Ďakujeme za registráciu.',
            'body' => 'V aplikácii môžete spravovať faktúry, firemné profily a zálohy na Google Drive.',
            'activate_button' => 'Aktivovať účet',
            'login_button' => 'Prihlásiť sa',
            'expires' => 'Odkaz na aktiváciu je platný :minutes minút.',
            'footer' => 'Ak ste si účet nevytvorili vy, tento e-mail môžete ignorovať.',
        ],
        'reset_password' => [
            'subject' => 'Obnovenie hesla – :app',
            'heading' => 'Obnovenie hesla',
            'intro' => 'Dobrý deň, :name,',
            'body' => 'Dostali sme žiadosť o obnovenie hesla k vášmu účtu.',
            'button' => 'Nastaviť nové heslo',
            'expires' => 'Odkaz je platný :minutes minút.',
            'footer' => 'Ak ste o obnovenie hesla nežiadali, tento e-mail môžete ignorovať.',
        ],
        'invoice_sent' => [
            'heading' => 'Faktúra :number',
            'payment_section' => 'Údaje k platbe',
            'amount' => 'Suma',
            'payment_method' => 'Spôsob úhrady',
            'variable_symbol' => 'Variabilný symbol',
            'iban' => 'IBAN',
            'swift' => 'SWIFT',
            'due_date' => 'Dátum splatnosti',
            'pay_by_square' => 'PAY by square',
            'qr_alt' => 'PAY by square QR kód',
            'attachment_hint' => 'Faktúra v PDF je priložená k tomuto e-mailu.',
            'footer' => 'Tento e-mail bol odoslaný z aplikácie :app.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shell / navigácia
    |--------------------------------------------------------------------------
    */

    'shell' => [
        'welcome_back' => 'Vitajte späť, :name',
        'nav' => [
            'invoices' => 'Faktúry',
            'logout' => 'Odhlásiť sa',
            'menu' => 'Menu',
            'settings' => 'Nastavenia účtu',
            'edit_company_profile' => 'Upraviť profil firmy',
            'select_company' => 'Vyberte firmu',
            'add_company' => 'Pridať firmu',
        ],
        'mobile_nav' => [
            'invoices' => 'Faktúry',
            'edit_company_profile' => 'Upraviť profil firmy',
            'company' => 'Firma',
            'add_company' => 'Pridať firmu',
            'settings' => 'Nastavenia účtu',
            'logout' => 'Odhlásiť sa',
        ],
        'redirecting' => 'Presmerovávam do aplikácie…',
    ],

    /*
    |--------------------------------------------------------------------------
    | Nastavenia účtu
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'title' => 'Nastavenia účtu',
        'subtitle' => 'Spravujte prihlasovacie údaje a prepojené účty.',

        'email' => [
            'section' => 'E-mail',
            'label' => 'E-mailová adresa',
            'submit' => 'Uložiť e-mail',
        ],

        'password' => [
            'section' => 'Heslo',
            'current' => 'Aktuálne heslo',
            'new' => 'Nové heslo',
            'confirmation' => 'Potvrdenie hesla',
            'submit' => 'Zmeniť heslo',
            'google_only' => 'Účet je prihlásený výhradne cez Google. Pre zmenu hesla si najprv nastavte heslo cez obnovenie hesla.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Profil firmy
    |--------------------------------------------------------------------------
    */

    'company' => [
        'create_title' => 'Vytvoriť profil firmy',
        'edit_title' => 'Upraviť profil firmy',
        'subtitle' => 'Vyplňte údaje o vašej firme alebo živnosti. Názov firmy môžete vyhľadať v registri.',

        'sections' => [
            'basic' => 'Základné údaje',
            'registry' => 'Register',
            'contact' => 'Kontakt',
            'logo_stamp' => 'Logo a pečiatka / podpis',
        ],

        'logo_stamp_hint' => 'Nahrajte PNG súbory v pomere strán 4:3.',

        'fields' => [
            'name' => 'Názov firmy / živnosti',
            'name_placeholder' => 'Začnite písať názov alebo IČO...',
            'street' => 'Ulica a číslo',
            'postal_code' => 'PSČ',
            'city' => 'Mesto / obec',
            'country' => 'Krajina',
            'ico' => 'IČO',
            'dic' => 'DIČ',
            'taxpayer_type' => 'Typ platiteľa',
            'ic_dph' => 'IČ DPH',
            'registry' => 'Zapísaná v registri',
            'email' => 'E-mail',
            'phone' => 'Telefón',
            'web' => 'Web',
            'logo' => 'Logo',
            'stamp' => 'Pečiatka / podpis',
        ],

        'delete_confirm' => 'Potvrdiť vymazanie firmy',
        'delete' => 'Vymazať firmu',
        'save' => 'Uložiť',
        'saving' => 'Ukladám...',
        'create' => 'Vytvoriť',
        'creating' => 'Vytváram...',
    ],

    /*
    |--------------------------------------------------------------------------
    | Faktúry
    |--------------------------------------------------------------------------
    */

    'invoices' => [
        'title' => 'Faktúra',
        'new_title' => 'Nová faktúra',

        'dashboard' => [
            'empty_title' => 'Faktúra',
            'empty_description' => 'Vyberte faktúru zo zoznamu alebo vytvorte novú.',
        ],

        'documents_count' => '{0} :count dokladov|{1} :count doklad|[2,4] :count doklady|[5,*] :count dokladov',

        'list' => [
            'search_label' => 'Hľadať faktúry',
            'search_placeholder' => 'Hľadať podľa firmy, IČO alebo sumy...',
            'create' => 'Vytvoriť faktúru',
            'filter' => 'Filter',
            'sort' => 'Zoradiť',
            'empty' => 'Zatiaľ nemáte žiadne faktúry.',
            'paid_total' => 'Uhradené:',
            'unpaid_total' => 'Neuhradené:',
            'emailed_at' => 'Odoslané :date',
            'opened_at' => 'Otvorené :date',
            'email_sent' => 'E-mail odoslaný',
            'email_opened' => 'E-mail otvorený',
            'menu' => 'Menu faktúry',
        ],

        'actions' => [
            'close' => 'Zavrieť faktúru',
            'close_panel' => 'Zavrieť',
            'cancel' => 'Zrušiť',
            'preview' => 'Zobraziť',
            'print' => 'Tlačiť',
            'send' => 'Odoslať',
            'download' => 'Stiahnuť',
            'duplicate' => 'Kópia',
            'save' => 'Uložiť faktúru',
            'open' => 'Otvoriť',
            'payments' => 'Úhrady',
            'create_copy' => 'Vytvoriť kópiu',
            'delete' => 'Vymazať',
            'add_item' => 'Pridať položku',
            'remove_item' => 'Odstrániť položku',
            'lock' => 'Zamknúť',
            'unlock' => 'Odomknúť',
        ],

        'locked' => [
            'label' => 'Zamknutá',
            'banner' => 'Táto faktúra je zamknutá. Nie je možné upravovať údaje ani pridávať úhradu, kým ju neodomknete.',
            'lock_title' => 'Zamknúť faktúru',
            'unlock_title' => 'Odomknúť faktúru',
            'lock_description' => 'Po zamknutí nebude možné upravovať údaje faktúry ani pridať úhradu, kým ju znovu neodomknete.',
            'unlock_description' => 'Po odomknutí bude možné faktúru opäť upravovať a pridávať k nej úhradu.',
            'cancel' => 'Zrušiť',
            'lock_submit' => 'Zamknúť faktúru',
            'unlock_submit' => 'Odomknúť faktúru',
        ],

        'form' => [
            'partner_name' => 'Názov firmy (partner)',
            'partner_placeholder' => 'Partner alebo vyhľadajte v registri...',
            'issue_date' => 'Dátum vystavenia',
            'delivery_date' => 'Dátum dodania',
            'due_days' => 'Splatnosť (dni)',
            'due_date' => 'Dátum splatnosti',
            'identified_person' => 'Identifikovaná osoba pre DPH (§7, 7a)',
            'number' => 'Variabilný symbol (číslo faktúry)',
            'currency' => 'Mena',
            'exchange_rate' => 'Kurz',
            'iban' => 'IBAN / číslo účtu',
            'iban_placeholder' => 'SK... alebo číslo účtu',
            'iban_hint' => 'Ponúka výber z účtov už použitých na faktúrach.',
            'payment_method' => 'Spôsob úhrady',
            'items' => 'Položky',
            'item_name' => 'Názov položky',
            'quantity' => 'Množstvo',
            'unit' => 'MJ',
            'price' => 'Cena',
            'total' => 'Spolu',
            'payment_section' => 'Úhrada',
            'payment_date' => 'Dátum úhrady',
            'payment_amount' => 'Suma',
            'grand_total_label' => 'Celková suma faktúry',
            'logo' => 'Logo',
            'stamp' => 'Pečiatka / podpis',
            'logo_default_hint' => 'Predvolené z profilu firmy',
            'stamp_default_hint' => 'Predvolená z profilu firmy',
        ],

        'filter' => [
            'title' => 'Filter faktúr',
            'period' => 'Obdobie',
            'periods' => [
                'current_month' => 'Aktuálny mesiac',
                'last_month' => 'Minulý mesiac',
                'current_year' => 'Aktuálny rok',
                'last_year' => 'Minulý rok',
                'all' => 'Všetko',
                'custom' => 'Vlastné',
            ],
            'date_from' => 'Vystavené od',
            'date_to' => 'Vystavené do',
            'partner' => 'Partner',
            'partner_search' => 'Hľadať partnera...',
            'all_partners' => 'Všetci partneri',
            'clear' => 'Vyčistiť filter',
            'cancel' => 'Zrušiť',
            'apply' => 'Použiť',
        ],

        'sort' => [
            'title' => 'Zoradiť faktúry',
            'field_label' => 'Zoradiť podľa',
            'fields' => [
                'number' => 'Číslo faktúry',
                'partner_name' => 'Názov partnera',
                'total' => 'Celková suma',
            ],
            'direction_label' => 'Smer',
            'directions' => [
                'asc' => 'Vzostupne (A–Z, 0–9)',
                'desc' => 'Zostupne (Z–A, 9–0)',
            ],
            'cancel' => 'Zrušiť',
            'apply' => 'Použiť',
        ],

        'payment' => [
            'title' => 'Úhrada faktúry',
            'date' => 'Dátum úhrady',
            'method' => 'Spôsob úhrady',
            'amount' => 'Suma',
            'close' => 'Zavrieť',
            'submit' => 'Pridať úhradu',
        ],

        'delete' => [
            'title' => 'Vymazať faktúru',
            'description' => 'Naozaj chcete natrvalo vymazať túto faktúru? Táto akcia sa nedá vrátiť späť.',
            'confirm' => 'Rozumiem, že faktúra bude natrvalo odstránená vrátane položiek a histórie odoslania.',
            'cancel' => 'Zrušiť',
            'submit' => 'Vymazať faktúru',
            'submitting' => 'Mažem...',
        ],

        'preview' => [
            'title' => 'Náhľad faktúry',
            'close' => 'Zavrieť náhľad',
            'close_btn' => 'Zavrieť',
        ],

        'email' => [
            'title' => 'Odoslať faktúru e-mailom',
            'to' => 'Komu',
            'to_placeholder' => 'partner@firma.sk',
            'cc' => 'Kópia',
            'cc_placeholder' => 'volitelne@firma.sk',
            'from' => 'Od (odpoveď na)',
            'from_hint' => 'E-mail sa odošle cez server aplikácie. Pole „Od“ slúži ako adresa pre odpoveď (Reply-To), nie ako odosielateľ z vášho poštového klienta.',
            'locale' => 'Jazyk e-mailu',
            'locale_hint' => 'Jazyk textu e-mailu a bloku s údajmi k platbe. Predmet a text sa pri zmene jazyka automaticky aktualizujú.',
            'subject' => 'Predmet',
            'body' => 'Text e-mailu',
            'body_hint' => 'Pod textom sa automaticky doplnia údaje k platbe a PAY by square QR kód (pri bankovom prevode).',
            'attachment' => 'Ďalšia príloha (voliteľné)',
            'attachment_hint' => 'Faktúra v PDF sa priloží automaticky.',
            'close' => 'Zavrieť',
            'submit' => 'Odoslať',
            'submitting' => 'Odosielam...',
            'subject_template' => 'Faktúra :number (:amount)',
            'default_signature' => 'Váš dodávateľ',
            'default_body' => "Dobrý deň,\n\nv prílohe Vám zasielam faktúru. Nižšie nájdete údaje k platbe.\n\nS pozdravom\n:signature",
            'payment_details' => 'Údaje k platbe',
            'payment_sum' => 'Suma:',
            'payment_method' => 'Spôsob úhrady:',
            'payment_variable_symbol' => 'Variabilný symbol:',
            'payment_iban' => 'IBAN:',
            'payment_swift' => 'SWIFT:',
            'payment_due_date' => 'Dátum splatnosti:',
            'fallback_title' => 'Faktúra',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dokument (náhľad / PDF)
    |--------------------------------------------------------------------------
    */

    'document' => [
        'title' => 'Faktúra :number',
        'supplier' => 'Dodávateľ',
        'customer' => 'Odberateľ',
        'contact_details' => 'Kontaktné údaje',
        'ico' => 'IČO:',
        'dic' => 'DIČ:',
        'ic_dph' => 'IČ DPH:',
        'issue_date' => 'Dátum vystavenia:',
        'delivery_date' => 'Dátum dodania:',
        'due_date' => 'Dátum splatnosti:',
        'amount' => 'Suma',
        'payment_method' => 'Spôsob úhrady:',
        'variable_symbol' => 'Variabilný symbol:',
        'iban' => 'IBAN:',
        'swift' => 'SWIFT:',
        'identified_person_note' => 'Identifikovaná osoba pre DPH podľa §7, 7a',
        'table' => [
            'position' => '#',
            'name' => 'Názov položky',
            'quantity' => 'Množstvo',
            'unit' => 'MJ',
            'price' => 'Cena',
            'total' => 'Spolu',
            'empty' => 'Žiadne položky',
        ],
        'grand_total' => 'Celková suma',
        'stamp_label' => 'Pečiatka a podpis',
        'pay_by_square' => 'PAY by square',
        'qr_generating' => 'Generujem QR...',
        'qr_error' => 'QR sa nepodarilo vygenerovať',
        'qr_alt' => 'PAY by square QR kód',
        'logo_alt' => 'Logo',
        'stamp_alt' => 'Pečiatka / podpis',
        'pay_note' => 'Faktura :number',
    ],

    /*
    |--------------------------------------------------------------------------
    | Enumerácie
    |--------------------------------------------------------------------------
    */

    'enums' => [
        'invoice_status' => [
            'paid' => 'Uhradená',
            'unpaid' => 'Neuhradená',
            'overdue' => 'Po splatnosti',
        ],
        'payment_method' => [
            'cash' => 'Hotovosť',
            'bank_transfer' => 'Bankový prevod',
        ],
        'taxpayer_type' => [
            'neplatitel_dph' => 'Neplatiteľ DPH',
            'platitel_dph' => 'Platiteľ DPH',
            'identifikovana_osoba' => 'Identifikovaná osoba pre DPH',
        ],
        'user_role' => [
            'user' => 'Používateľ',
            'admin' => 'Administrátor',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validácia (vlastné správy)
    |--------------------------------------------------------------------------
    */

    'validation' => [
        'auth' => [
            'email_required' => 'E-mail je povinný.',
            'email_invalid' => 'Zadajte platnú e-mailovú adresu.',
            'password_required' => 'Heslo je povinné.',
            'invalid_credentials' => 'Neplatné prihlasovacie údaje.',
            'rate_limited' => 'Príliš veľa pokusov. Skúste znova o :seconds sekúnd.',
            'name_required' => 'Meno je povinné.',
            'email_unique' => 'Tento e-mail je už registrovaný.',
            'password_confirmed' => 'Heslá sa nezhodujú.',
        ],
        'settings' => [
            'email_required' => 'E-mail je povinný.',
            'email_invalid' => 'Zadajte platnú e-mailovú adresu.',
            'email_unique' => 'Tento e-mail je už používaný.',
            'current_password_required' => 'Aktuálne heslo je povinné.',
            'current_password_invalid' => 'Aktuálne heslo nie je správne.',
            'password_required' => 'Nové heslo je povinné.',
            'password_confirmed' => 'Heslá sa nezhodujú.',
            'no_password_set' => 'Účet nemá nastavené heslo. Najprv si ho nastavte cez registráciu alebo obnovenie hesla.',
            'google_unlink_password' => 'Pred odpojením Google účtu si najprv nastavte heslo.',
            'google_link_required' => 'Najprv prepojte Google účet s prístupom k Drive.',
            'google_import_failed' => 'Import zo Google Drive zlyhal: :error',
        ],
        'company' => [
            'name_required' => 'Názov firmy je povinný.',
            'country_required' => 'Krajina je povinná.',
            'email_invalid' => 'Zadajte platný e-mail.',
            'web_invalid' => 'Zadajte platnú webovú adresu.',
            'logo_mimes' => 'Logo musí byť vo formáte PNG.',
            'stamp_mimes' => 'Pečiatka / podpis musí byť vo formáte PNG.',
        ],
        'invoice' => [
            'partner_name_required' => 'Názov partnera je povinný.',
            'number_required' => 'Variabilný symbol je povinný.',
            'number_exists' => 'Toto číslo faktúry už existuje.',
            'item_name_required' => 'Názov položky je povinný.',
            'stamp_mimes' => 'Pečiatka / podpis musí byť vo formáte PNG.',
            'logo_mimes' => 'Logo musí byť vo formáte PNG.',
            'payment_amount_mismatch' => 'Suma úhrady musí zodpovedať celkovej sume faktúry.',
            'payment_date_required' => 'Dátum úhrady je povinný.',
            'payment_method_required' => 'Spôsob úhrady je povinný.',
            'payment_amount_required' => 'Suma úhrady je povinná.',
            'delete_confirm_required' => 'Pre vymazanie faktúry zaškrtnite potvrdenie.',
            'email_check_before_send' => 'Pred odoslaním skontrolujte údaje faktúry.',
            'email_to_required' => 'Zadajte e-mail príjemcu.',
            'email_to_invalid' => 'E-mail príjemcu nie je platný.',
            'email_from_required' => 'Zadajte e-mail pre odpoveď.',
            'email_subject_required' => 'Predmet je povinný.',
            'email_body_required' => 'Text e-mailu je povinný.',
            'pdf_generation_failed' => 'Nepodarilo sa vygenerovať PDF faktúry.',
            'email_send_failed' => 'E-mail sa nepodarilo odoslať. Skontrolujte nastavenie pošty aplikácie.',
            'export_check_save' => 'Pred exportom skontrolujte a uložte faktúru.',
            'locked' => 'Faktúra je zamknutá a nedá sa upravovať ani k nej pridať úhrada.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Flash správy
    |--------------------------------------------------------------------------
    */

    'messages' => [
        'invoice_saved' => 'Faktúra bola uložená.',
        'invoice_sent' => 'Faktúra bola odoslaná e-mailom.',
        'invoice_deleted' => 'Faktúra :number bola vymazaná.',
        'payment_recorded' => 'Úhrada bola zaznamenaná.',
        'invoice_locked' => 'Faktúra :number bola zamknutá.',
        'invoice_unlocked' => 'Faktúra :number bola odomknutá.',
        'company_profile_saved' => 'Profil firmy bol uložený.',
        'email_updated' => 'E-mail bol aktualizovaný.',
        'password_changed' => 'Heslo bolo zmenené.',
        'google_unlinked' => 'Google účet bol odpojený.',
        'google_linked' => 'Google účet bol úspešne prepojený. Záloha sa spúšťa na pozadí.',
        'google_link_already_used' => 'Tento Google účet je už prepojený s iným používateľom.',
        'google_import_completed' => 'Import dokončený: :profiles profilov, :invoices faktúr. Záloha sa znova synchronizuje.',
        'registration_check_email' => 'Účet bol vytvorený. Skontrolujte e-mail a aktivujte účet kliknutím na odkaz.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Nahrávanie súborov
    |--------------------------------------------------------------------------
    */

    'upload' => [
        'badge' => 'PNG · 280 × 210 px',
        'drag_logo' => 'Pretiahnite logo sem',
        'drag_image' => 'Pretiahnite obrázok sem',
        'or_select' => 'alebo',
        'select_file' => 'vyberte súbor',
        'change' => 'Zmeniť',
        'remove' => 'Odstrániť',
        'uploading' => 'Nahrávam...',
        'logo_options' => 'Možnosti loga',
        'stamp_options' => 'Možnosti pečiatky',
    ],

    /*
    |--------------------------------------------------------------------------
    | Google účet a záloha
    |--------------------------------------------------------------------------
    */

    'google' => [
        'section_title' => 'Google účet a záloha',
        'description' => 'Prepojený Google účet slúži na zálohu profilov, faktúr a PDF na Google Drive.',
        'backup_auto' => 'Záloha sa spúšťa automaticky po každej zmene.',
        'backup_disabled' => 'Automatická synchronizácia je vypnutá v konfigurácii servera.',
        'import_hint' => 'Import obnoví dáta zo súboru backup.json.',
        'linked' => 'Google účet je prepojený',
        'import' => 'Importovať zo Drive',
        'importing' => 'Importujem...',
        'import_confirm' => 'Importovať dáta z Google Drive? Existujúce profily a faktúry sa aktualizujú alebo doplnia.',
        'unlink' => 'Odpojiť Google',
        'link' => 'Prepojiť Google účet',
        'last_backup' => 'Posledná záloha: :date',
        'syncing' => 'Prebieha synchronizácia na Google Drive...',
        'sync_failed' => 'Posledná synchronizácia zlyhala: :error',
        'sync_success' => 'Záloha na Google Drive je aktuálna.',
        'sync_pending' => 'Záloha sa spustí po prvej zmene alebo po prepojení účtu.',
        'refresh_token_missing' => 'Chýba refresh token pre Drive. Odpojte Google a znova ho prepojte (s potvrdením prístupu).',
    ],

];
