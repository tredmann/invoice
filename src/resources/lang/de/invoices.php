<?php

return [
    'invoice' => 'Rechnung',
    'invoices' => 'Rechnungen',

    // views and headers
    'show' => 'Rechnungsdaten',
    'line_items' => __('lineItems.line_items'),
    'conclude' => 'Rechnung abschließen',
    'conclude_sentence' => 'Wählen Sie Fälligkeit und Leistungszeitraum aus.',

    // constants
    'days_till_due.7' => '7 Tage',
    'days_till_due.14' => '14 Tage',
    'days_till_due.30' => '30 Tage',

    'status.draft' => 'Entwurf',
    'status.open' => 'Offen',
    'status.overdue' => 'Überfällig',
    'status.paid' => 'Bezahlt',
    'status.open pdf error' => 'Eröffnungsfehler: PDF!',
    'status.cancelled' => 'Storniert',
    'status.cancellation invoice' => 'Stornierungsrechnung für :invoice_no',

    'mail_status.not mailable' => 'Nicht bereit',
    'mail_status.mailable' => 'Bereit',
    'mail_status.mailed' => 'Verschickt',
    'mail_status.mailing' => 'Wird verschickt',
    'mail_status.mail error' => 'Emailfehler!',

    // triggerMenu
    'delete_confirm' => 'Wirklich diese Rechnung löschen?',
    'show_pdf' => 'PDF zeigen',
    'send' => 'Rechnung versenden',
    'paid' => 'Bezahlt am',
    'cancel' => 'Stornieren',
    'mail_attachment' => 'Anhang:',

    // buttons
    'add' => 'Rechnung hinzufügen',

    // cancellation invoice
    'cancellation_invoice_for_invoice' => 'Stornierungsrechnung zur Rechnung :invoice_no',

    // mail
    'mail' => [
        'salute' => [
            'male' => 'Sehr geehrter Herr :first_name :last_name',
            'female' => 'Sehr geehrte Frau :first_name :last_name',
            'diverse' => 'Hallo :first_name :last_name',
            'company' => 'Sehr geehrte Damen und Herren von :company',
        ],
    ],

    'invoice_mail' => [
        'paragraph1' => 'anbei erhalten Sie die Rechnung für die von uns bezogenen Leistungen. Nähere Informationen entnehmen Sie bitte der angehängten PDF-Datei.',
        'paragraph2' => 'Bitte überweisen Sie den vollständigen Rechnungsbetrag in Höhe von :total_with_tax mit Angabe der Rechnungsnummer :invoice_no bis zum :date_due auf folgendes Konto:',
        'thanks' => 'Vielen Dank für das Vertrauen.',
        'greetings' => 'Viele Grüße',
    ],

    'invoice_cancellation_mail' => [
        'paragraph1' => 'anbei erhalten Sie eine Stornierungsrechnung für die von uns bezogenen Leistungen. Nähere Informationen entnehmen Sie bitte der angehängten PDF-Datei.',
        'paragraph2' => 'Wir überweisen Ihnen den vollständigen Rechnungsbetrag in Höhe von :total_with_tax - falls bereits überwiesen - unter Angabe der Rechnungsnummer :invoice_no zurück auf Ihr Konto:',
        'thanks' => 'Vielen Dank für das Vertrauen.',
        'greetings' => 'Viele Grüße',
    ],

];
