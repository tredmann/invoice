<?php

return [
    'invoice' => 'Invoice',
    'invoices' => 'Invoices',

    // views and headers
    'show' => 'Invoice data',
    'line_items' => __('lineItems.line_items'),
    'conclude' => 'Conclude invoice',
    'conclude_sentence' => 'Choose how many days the invoice may be due and enter when it was performed.',

    // constants
    'days_till_due.7' => '7 Tage',
    'days_till_due.14' => '14 Tage',
    'days_till_due.30' => '30 Tage',

    'status.draft' => 'Draft',
    'status.open' => 'Open',
    'status.overdue' => 'Overdue',
    'status.paid' => 'Paid',
    'status.open error' => 'Open Error: PDF!',
    'status:cancelled' => 'Cancelled',
    'status:cancellation_invoice' => 'Cancellation Invoice',

    'mail_status.not mailable' => 'Not ready',
    'mail_status.mailable' => 'Ready',
    'mail_status.mailed' => 'Sent',
    'mail_status.mailing' => 'Pending',
    'mail_status.mail error' => 'Email-Error!',

    // triggerMenu
    'delete_confirm' => 'Do you really want to delete this invoice?',
    'show_pdf' => 'Show PDF',
    'send' => 'Send invoice',
    'paid' => 'Was paid at',
    'cancel' => 'Cancel',
    'mail_attachment' => 'Attachment',

    // buttons
    'add' => 'Add Invoice',

    // cancellation invoice
    'cancelled_invoice_for_invoice' => 'Cancelled invoice for invoice',

    // mail
    'invoice_mail' => [
        'salute' => [
            'male' => 'Dear Mr. :first_name :last_name',
            'female' => 'Dear Mrs. :first_name :last_name',
            'diverse' => 'Dear :first_name :last_name',
            'company' => 'Dear Sir or Madam of :company',
        ],
        'paragraph1' => 'enclosed you will find the invoice for the services purchased from us. For more information, please refer to the attached PDF file.',
        'paragraph2' => 'Please transfer the full amount of :total_with_tax, quoting the invoice number :invoice_no by :date_due to the following account:',
        'thanks' => 'Thanks for your trust.',
        'greetings' => 'Sincerly',
    ],

    'invoice_cancellation_cancellation' => [
        'salute' => [
            'male' => 'Dear Mr. :first_name :last_name',
            'female' => 'Dear Mrs. :first_name :last_name',
            'diverse' => 'Dear :first_name :last_name',
            'company' => 'Dear Sir or Madam of :company',
        ],
        'paragraph1' => 'enclosed you will find the invoice for the services purchased from us. For more information, please refer to the attached PDF file.',
        'paragraph2' => 'Please transfer the full amount of :total_with_tax, quoting the invoice number :invoice_no by :date_due to the following account:',
        'thanks' => 'Thanks for your trust.',
        'greetings' => 'Sincerly',
    ],
];
