<?php

return [

    /*
    |--------------------------------------------------------------------------
    | General
    |--------------------------------------------------------------------------
    */

    'toggle_dark_mode' => 'Toggle dark mode',
    'switch_language' => 'Switch language',
    'tagline' => 'Invoice management made simple and clear',

    'or' => 'or',
    'dash' => '—',
    'currency_default' => 'EUR',
    'unit_default' => 'pcs',

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'login' => [
            'title' => 'Sign in',
            'subtitle' => 'Sign in to your account',
            'email' => 'Email',
            'password' => 'Password',
            'remember' => 'Remember me',
            'submit' => 'Sign in',
            'submitting' => 'Signing in...',
            'google' => 'Sign in with Google',
            'no_account' => "Don't have an account?",
            'register_link' => 'Register',
            'forgot_password' => 'Forgot password?',
        ],
        'register' => [
            'title' => 'Register',
            'subtitle' => 'Create a new account',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Confirm password',
            'submit' => 'Register',
            'submitting' => 'Registering...',
            'google' => 'Register with Google',
            'has_account' => 'Already have an account?',
            'login_link' => 'Sign in',
        ],
        'forgot_password' => [
            'title' => 'Reset password',
            'subtitle' => 'Enter your email and we will send you a link to set a new password.',
            'email' => 'Email',
            'submit' => 'Send reset link',
            'submitting' => 'Sending...',
            'sent' => 'If an account with this email exists, a password reset link has been sent.',
            'back_to_login' => 'Back to sign in',
        ],
        'reset_password' => [
            'title' => 'New password',
            'subtitle' => 'Enter a new password for your account.',
            'email' => 'Email',
            'password' => 'New password',
            'password_confirmation' => 'Confirm password',
            'submit' => 'Set password',
            'submitting' => 'Saving...',
            'success' => 'Your password has been changed. You can sign in now.',
            'back_to_login' => 'Back to sign in',
        ],
        'verification' => [
            'modal_title' => 'Verify your email',
            'modal_description' => 'Account :email is not activated yet. Check your inbox and click the activation link.',
            'expires_hint' => 'The activation link is valid for :minutes minutes.',
            'resend' => 'Resend email',
            'resending' => 'Sending...',
            'resend_wait' => 'Resend in :seconds s',
            'resent' => 'Verification email has been sent.',
            'close' => 'Close',
            'verified_success' => 'Email verified. You can sign in now.',
            'already_verified' => 'Email is already verified. You can sign in.',
            'link_invalid' => 'The activation link is invalid.',
            'link_expired' => 'The activation link has expired. Sign in and request a new one.',
        ],
    ],

    'emails' => [
        'welcome' => [
            'subject' => 'Welcome to :app',
            'heading' => 'Welcome, :name!',
            'intro' => 'Thank you for registering.',
            'body' => 'You can manage invoices, company profiles and Google Drive backups in the app.',
            'activate_button' => 'Activate account',
            'login_button' => 'Sign in',
            'expires' => 'The activation link is valid for :minutes minutes.',
            'footer' => 'If you did not create this account, you can ignore this email.',
        ],
        'reset_password' => [
            'subject' => 'Reset password – :app',
            'heading' => 'Reset password',
            'intro' => 'Hello, :name,',
            'body' => 'We received a request to reset the password for your account.',
            'button' => 'Set new password',
            'expires' => 'This link is valid for :minutes minutes.',
            'footer' => 'If you did not request a password reset, you can ignore this email.',
        ],
        'invoice_sent' => [
            'heading' => 'Invoice :number',
            'payment_section' => 'Payment details',
            'amount' => 'Amount',
            'payment_method' => 'Payment method',
            'variable_symbol' => 'Variable symbol',
            'iban' => 'IBAN',
            'swift' => 'SWIFT',
            'due_date' => 'Due date',
            'pay_by_square' => 'PAY by square',
            'qr_alt' => 'PAY by square QR code',
            'attachment_hint' => 'The invoice PDF is attached to this email.',
            'footer' => 'This email was sent from :app.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shell / navigation
    |--------------------------------------------------------------------------
    */

    'shell' => [
        'welcome_back' => 'Welcome back, :name',
        'nav' => [
            'invoices' => 'Invoices',
            'logout' => 'Sign out',
            'menu' => 'Menu',
            'settings' => 'Account settings',
            'edit_company_profile' => 'Edit company profile',
            'select_company' => 'Select company',
            'add_company' => 'Add company',
        ],
        'mobile_nav' => [
            'invoices' => 'Invoices',
            'edit_company_profile' => 'Edit company profile',
            'company' => 'Company',
            'add_company' => 'Add company',
            'settings' => 'Account settings',
            'logout' => 'Sign out',
        ],
        'redirecting' => 'Redirecting to the app…',
    ],

    /*
    |--------------------------------------------------------------------------
    | Account settings
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'title' => 'Account settings',
        'subtitle' => 'Manage your login credentials and linked accounts.',

        'email' => [
            'section' => 'Email',
            'label' => 'Email address',
            'submit' => 'Save email',
        ],

        'password' => [
            'section' => 'Password',
            'current' => 'Current password',
            'new' => 'New password',
            'confirmation' => 'Confirm password',
            'submit' => 'Change password',
            'google_only' => 'This account is signed in exclusively via Google. To change your password, first set one through password recovery.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Company profile
    |--------------------------------------------------------------------------
    */

    'company' => [
        'create_title' => 'Create company profile',
        'edit_title' => 'Edit company profile',
        'subtitle' => 'Fill in your company or sole proprietorship details. You can search for the company name in the registry.',

        'sections' => [
            'basic' => 'Basic details',
            'registry' => 'Registry',
            'contact' => 'Contact',
            'logo_stamp' => 'Logo and stamp / signature',
        ],

        'logo_stamp_hint' => 'Upload PNG files with a 4:3 aspect ratio.',

        'fields' => [
            'name' => 'Company / business name',
            'name_placeholder' => 'Start typing a name or company ID...',
            'street' => 'Street and number',
            'postal_code' => 'Postal code',
            'city' => 'City / town',
            'country' => 'Country',
            'ico' => 'Company ID',
            'dic' => 'Tax ID',
            'taxpayer_type' => 'Taxpayer type',
            'ic_dph' => 'VAT ID',
            'registry' => 'Registered in',
            'email' => 'Email',
            'phone' => 'Phone',
            'web' => 'Website',
            'logo' => 'Logo',
            'stamp' => 'Stamp / signature',
        ],

        'delete_confirm' => 'Confirm company deletion',
        'delete' => 'Delete company',
        'save' => 'Save',
        'saving' => 'Saving...',
        'create' => 'Create',
        'creating' => 'Creating...',
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoices
    |--------------------------------------------------------------------------
    */

    'invoices' => [
        'title' => 'Invoice',
        'new_title' => 'New invoice',

        'dashboard' => [
            'empty_title' => 'Invoice',
            'empty_description' => 'Select an invoice from the list or create a new one.',
        ],

        'documents_count' => '{0} :count documents|{1} :count document|[2,*] :count documents',

        'list' => [
            'search_label' => 'Search invoices',
            'search_placeholder' => 'Search by company, company ID, or amount...',
            'create' => 'Create invoice',
            'filter' => 'Filter',
            'sort' => 'Sort',
            'empty' => 'You do not have any invoices yet.',
            'paid_total' => 'Paid:',
            'unpaid_total' => 'Unpaid:',
            'emailed_at' => 'Sent :date',
            'menu' => 'Invoice menu',
        ],

        'actions' => [
            'close' => 'Close invoice',
            'close_panel' => 'Close',
            'cancel' => 'Cancel',
            'preview' => 'Preview',
            'print' => 'Print',
            'send' => 'Send',
            'download' => 'Download',
            'duplicate' => 'Copy',
            'save' => 'Save invoice',
            'open' => 'Open',
            'payments' => 'Payments',
            'create_copy' => 'Create copy',
            'delete' => 'Delete',
            'add_item' => 'Add item',
            'remove_item' => 'Remove item',
        ],

        'form' => [
            'partner_name' => 'Company name (partner)',
            'partner_placeholder' => 'Partner or search in the registry...',
            'issue_date' => 'Issue date',
            'delivery_date' => 'Delivery date',
            'due_days' => 'Due (days)',
            'due_date' => 'Due date',
            'identified_person' => 'Identified person for VAT (§7, 7a)',
            'number' => 'Variable symbol (invoice number)',
            'currency' => 'Currency',
            'exchange_rate' => 'Exchange rate',
            'iban' => 'IBAN / account number',
            'iban_placeholder' => 'SK... or account number',
            'iban_hint' => 'Offers a selection from accounts already used on invoices.',
            'payment_method' => 'Payment method',
            'items' => 'Items',
            'item_name' => 'Item name',
            'quantity' => 'Quantity',
            'unit' => 'Unit',
            'price' => 'Price',
            'total' => 'Total',
            'payment_section' => 'Payment',
            'payment_date' => 'Payment date',
            'payment_amount' => 'Amount',
            'grand_total_label' => 'Invoice total',
            'logo' => 'Logo',
            'stamp' => 'Stamp / signature',
            'logo_default_hint' => 'Default from company profile',
            'stamp_default_hint' => 'Default from company profile',
        ],

        'filter' => [
            'title' => 'Invoice filter',
            'period' => 'Period',
            'periods' => [
                'current_month' => 'Current month',
                'last_month' => 'Last month',
                'current_year' => 'Current year',
                'last_year' => 'Last year',
                'all' => 'All',
                'custom' => 'Custom',
            ],
            'date_from' => 'Issued from',
            'date_to' => 'Issued to',
            'partner' => 'Partner',
            'partner_search' => 'Search partner...',
            'all_partners' => 'All partners',
            'clear' => 'Clear filter',
            'cancel' => 'Cancel',
            'apply' => 'Apply',
        ],

        'sort' => [
            'title' => 'Sort invoices',
            'field_label' => 'Sort by',
            'fields' => [
                'number' => 'Invoice number',
                'partner_name' => 'Partner name',
                'total' => 'Total amount',
            ],
            'direction_label' => 'Direction',
            'directions' => [
                'asc' => 'Ascending (A–Z, 0–9)',
                'desc' => 'Descending (Z–A, 9–0)',
            ],
            'cancel' => 'Cancel',
            'apply' => 'Apply',
        ],

        'payment' => [
            'title' => 'Invoice payment',
            'date' => 'Payment date',
            'method' => 'Payment method',
            'amount' => 'Amount',
            'close' => 'Close',
            'submit' => 'Add payment',
        ],

        'delete' => [
            'title' => 'Delete invoice',
            'description' => 'Are you sure you want to permanently delete this invoice? This action cannot be undone.',
            'confirm' => 'I understand that the invoice will be permanently removed, including items and send history.',
            'cancel' => 'Cancel',
            'submit' => 'Delete invoice',
            'submitting' => 'Deleting...',
        ],

        'preview' => [
            'title' => 'Invoice preview',
            'close' => 'Close preview',
            'close_btn' => 'Close',
        ],

        'email' => [
            'title' => 'Send invoice by email',
            'to' => 'To',
            'to_placeholder' => 'partner@company.com',
            'cc' => 'Cc',
            'cc_placeholder' => 'optional@company.com',
            'from' => 'From (reply-to)',
            'from_hint' => 'The email is sent through the application server. The "From" field serves as the reply-to address, not as the sender from your mail client.',
            'locale' => 'Email language',
            'locale_hint' => 'Language of the email text and payment details block. Subject and body update automatically when you change the language.',
            'subject' => 'Subject',
            'body' => 'Email body',
            'body_hint' => 'Payment details and a PAY by square QR code (for bank transfer) are automatically appended below the text.',
            'attachment' => 'Additional attachment (optional)',
            'attachment_hint' => 'The invoice PDF is attached automatically.',
            'close' => 'Close',
            'submit' => 'Send',
            'submitting' => 'Sending...',
            'subject_template' => 'Invoice :number (:amount)',
            'default_signature' => 'Your supplier',
            'default_body' => "Hello,\n\nPlease find the invoice attached. Payment details are provided below.\n\nBest regards\n:signature",
            'payment_details' => 'Payment details',
            'payment_sum' => 'Amount:',
            'payment_method' => 'Payment method:',
            'payment_variable_symbol' => 'Variable symbol:',
            'payment_iban' => 'IBAN:',
            'payment_swift' => 'SWIFT:',
            'payment_due_date' => 'Due date:',
            'fallback_title' => 'Invoice',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Document (preview / PDF)
    |--------------------------------------------------------------------------
    */

    'document' => [
        'title' => 'Invoice :number',
        'supplier' => 'Supplier',
        'customer' => 'Customer',
        'contact_details' => 'Contact details',
        'ico' => 'Company ID:',
        'dic' => 'Tax ID:',
        'ic_dph' => 'VAT ID:',
        'issue_date' => 'Issue date:',
        'delivery_date' => 'Delivery date:',
        'due_date' => 'Due date:',
        'amount' => 'Amount',
        'payment_method' => 'Payment method:',
        'variable_symbol' => 'Variable symbol:',
        'iban' => 'IBAN:',
        'swift' => 'SWIFT:',
        'identified_person_note' => 'Identified person for VAT under §7, 7a',
        'table' => [
            'position' => '#',
            'name' => 'Item name',
            'quantity' => 'Quantity',
            'unit' => 'Unit',
            'price' => 'Price',
            'total' => 'Total',
            'empty' => 'No items',
        ],
        'grand_total' => 'Total amount',
        'stamp_label' => 'Stamp and signature',
        'pay_by_square' => 'PAY by square',
        'qr_generating' => 'Generating QR...',
        'qr_error' => 'Failed to generate QR',
        'qr_alt' => 'PAY by square QR code',
        'logo_alt' => 'Logo',
        'stamp_alt' => 'Stamp / signature',
        'pay_note' => 'Invoice :number',
    ],

    /*
    |--------------------------------------------------------------------------
    | Enumerations
    |--------------------------------------------------------------------------
    */

    'enums' => [
        'invoice_status' => [
            'paid' => 'Paid',
            'unpaid' => 'Unpaid',
            'overdue' => 'Overdue',
        ],
        'payment_method' => [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank transfer',
        ],
        'taxpayer_type' => [
            'neplatitel_dph' => 'Non-VAT payer',
            'platitel_dph' => 'VAT payer',
            'identifikovana_osoba' => 'Identified person for VAT',
        ],
        'user_role' => [
            'user' => 'User',
            'admin' => 'Administrator',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation (custom messages)
    |--------------------------------------------------------------------------
    */

    'validation' => [
        'auth' => [
            'email_required' => 'Email is required.',
            'email_invalid' => 'Enter a valid email address.',
            'password_required' => 'Password is required.',
            'invalid_credentials' => 'Invalid login credentials.',
            'rate_limited' => 'Too many attempts. Try again in :seconds seconds.',
            'name_required' => 'Name is required.',
            'email_unique' => 'This email is already registered.',
            'password_confirmed' => 'Passwords do not match.',
        ],
        'settings' => [
            'email_required' => 'Email is required.',
            'email_invalid' => 'Enter a valid email address.',
            'email_unique' => 'This email is already in use.',
            'current_password_required' => 'Current password is required.',
            'current_password_invalid' => 'Current password is incorrect.',
            'password_required' => 'New password is required.',
            'password_confirmed' => 'Passwords do not match.',
            'no_password_set' => 'The account has no password set. Set one first through registration or password recovery.',
            'google_unlink_password' => 'Set a password before unlinking your Google account.',
            'google_link_required' => 'First link a Google account with Drive access.',
            'google_import_failed' => 'Import from Google Drive failed: :error',
        ],
        'company' => [
            'name_required' => 'Company name is required.',
            'country_required' => 'Country is required.',
            'email_invalid' => 'Enter a valid email.',
            'web_invalid' => 'Enter a valid website address.',
            'logo_mimes' => 'Logo must be a PNG file.',
            'stamp_mimes' => 'Stamp / signature must be a PNG file.',
        ],
        'invoice' => [
            'partner_name_required' => 'Partner name is required.',
            'number_required' => 'Variable symbol is required.',
            'number_exists' => 'This invoice number already exists.',
            'item_name_required' => 'Item name is required.',
            'stamp_mimes' => 'Stamp / signature must be a PNG file.',
            'logo_mimes' => 'Logo must be a PNG file.',
            'payment_amount_mismatch' => 'Payment amount must match the invoice total.',
            'payment_date_required' => 'Payment date is required.',
            'payment_method_required' => 'Payment method is required.',
            'payment_amount_required' => 'Payment amount is required.',
            'delete_confirm_required' => 'Check the confirmation box to delete the invoice.',
            'email_check_before_send' => 'Review the invoice details before sending.',
            'email_to_required' => 'Enter the recipient email.',
            'email_to_invalid' => 'Recipient email is not valid.',
            'email_from_required' => 'Enter the reply-to email.',
            'email_subject_required' => 'Subject is required.',
            'email_body_required' => 'Email body is required.',
            'pdf_generation_failed' => 'Failed to generate invoice PDF.',
            'email_send_failed' => 'Failed to send email. Check the application mail settings.',
            'export_check_save' => 'Review and save the invoice before exporting.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Flash messages
    |--------------------------------------------------------------------------
    */

    'messages' => [
        'invoice_saved' => 'Invoice saved.',
        'invoice_sent' => 'Invoice sent by email.',
        'invoice_deleted' => 'Invoice :number was deleted.',
        'payment_recorded' => 'Payment recorded.',
        'company_profile_saved' => 'Company profile saved.',
        'email_updated' => 'Email updated.',
        'password_changed' => 'Password changed.',
        'google_unlinked' => 'Google account unlinked.',
        'google_linked' => 'Google account linked successfully. Backup is starting in the background.',
        'google_link_already_used' => 'This Google account is already linked to another user.',
        'google_import_completed' => 'Import completed: :profiles profiles, :invoices invoices. Backup will sync again.',
        'registration_check_email' => 'Account created. Check your email and activate your account using the link.',
    ],

    /*
    |--------------------------------------------------------------------------
    | File upload
    |--------------------------------------------------------------------------
    */

    'upload' => [
        'badge' => 'PNG · 280 × 210 px',
        'drag_logo' => 'Drag logo here',
        'drag_image' => 'Drag image here',
        'or_select' => 'or',
        'select_file' => 'select file',
        'change' => 'Change',
        'remove' => 'Remove',
        'uploading' => 'Uploading...',
        'logo_options' => 'Logo options',
        'stamp_options' => 'Stamp options',
    ],

    /*
    |--------------------------------------------------------------------------
    | Google account and backup
    |--------------------------------------------------------------------------
    */

    'google' => [
        'section_title' => 'Google account and backup',
        'description' => 'A linked Google account is used to back up profiles, invoices, and PDFs to Google Drive.',
        'backup_auto' => 'Backup runs automatically after every change.',
        'backup_disabled' => 'Automatic synchronization is disabled in the server configuration.',
        'import_hint' => 'Import restores data from the backup.json file.',
        'linked' => 'Google account is linked',
        'import' => 'Import from Drive',
        'importing' => 'Importing...',
        'import_confirm' => 'Import data from Google Drive? Existing profiles and invoices will be updated or added.',
        'unlink' => 'Unlink Google',
        'link' => 'Link Google account',
        'last_backup' => 'Last backup: :date',
        'syncing' => 'Syncing to Google Drive...',
        'sync_failed' => 'Last synchronization failed: :error',
        'sync_success' => 'Google Drive backup is up to date.',
        'sync_pending' => 'Backup will start after the first change or after linking the account.',
        'refresh_token_missing' => 'Drive refresh token is missing. Unlink Google and link it again (with access confirmation).',
    ],

];
