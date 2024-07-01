<?php

namespace App\Console\Commands;

use HTMLPurifier;
use App\Models\Email;
use App\Models\Movement;
use Illuminate\Console\Command;

class FetchEmails extends Command
{
    protected $signature = 'emails:fetch';

    protected $description = 'Fetch emails from Gmail and store them in the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $hostname = '{' . env('IMAP_HOST') . ':' . env('IMAP_PORT') . '/' . env('IMAP_ENCRYPTION') . '}INBOX';
        $username = env('IMAP_USERNAME');
        $password = env('IMAP_PASSWORD');

        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

        $emails = imap_search($inbox, 'UNSEEN');

        if ($emails) {
            rsort($emails);

            foreach ($emails as $email_number) {
                $overview = imap_fetch_overview($inbox, $email_number, 0)[0];
                $message = $this->getBody($inbox, $email_number);

                $subject = $this->decodeSubject($overview->subject);
                $from = $this->extractEmail($overview->from);
                $received_at = date("Y-m-d H:i:s", strtotime($overview->date));


                // Armazene o e-mail no banco de dados
                $email = Email::create([
                    'subject' => $subject,
                    'from' => $from,
                    'body' => $message,
                    'received_at' => $received_at,
                ]);

                $this->extractAndSaveMovement($email->id, $from, $message, $received_at);

                imap_setflag_full($inbox, $email_number, "\\Seen");
            }
        }

        imap_close($inbox);

        $this->info('Emails fetched successfully.');
    }


    /**
     * Extracts and saves movement data from the email content.
     *
     * @param mixed $id The ID of the email.
     * @param string $from The sender of the email.
     * @param string $body The body content of the email.
     * @return void
     */
    public function extractAndSaveMovement($id, $from, $body, $date)
    {
        if (empty($body)) {
            return;
        }

        if ($from === 'nao_responda@tjto.jus.br') {
            $patterns = [
                'organ' => '/Orgão:\s*(.*?)(?:\s+Processo:)/',
                'process' => '/Processo:\s*(\d{7}-\d{2}\.\d{4}\.\d{1}\.\d{2}\.\d{4})/',
                'judicial_class' => '/Classe Judicial:\s*(.*?)(?:\s+Evento Judicial:|\s+Partes do processo)/',
                'judicial_event' => '/Evento Judicial:\s*(.*?)(?:\s+Partes do processo)/',
                'authors' => '/Autor\(es\):\s*(.*?)(?:\s+Réu\(s\):)/',
                'defendants' => '/Réu\(s\):\s*(.*?)(?:\s+Visualizar Processo)/'
            ];

            $matches = [];

            foreach ($patterns as $key => $pattern) {
                preg_match($pattern, $body, $match);
                $matches[$key] = isset($match[1]) ? trim($match[1]) : null;
            }

            // dd($matches, $date);

            Movement::create([
                'email_id' => $id,
                'organ' => $matches['organ'],
                'process' => $matches['process'],
                'judicial_class' => $matches['judicial_class'],
                'judicial_event' => $matches['judicial_event'],
                'authors' => $matches['authors'],
                'defendants' => $matches['defendants'],
                'event_date' => $date,
            ]);
        }
    }

    /**
     * Decodificar o assunto do e-mail.
     *
     * @param string $subject
     * @return string
     */
    private function decodeSubject($subject)
    {
        $elements = imap_mime_header_decode($subject);
        $decoded = '';

        foreach ($elements as $element) {
            $decoded .= $element->text;
        }

        return $decoded;
    }

    /**
     * Extrair apenas o endereço de e-mail do campo from.
     *
     * @param string $from
     * @return string
     */
    private function extractEmail($from)
    {
        // Decodificar se necessário
        $decoded = imap_mime_header_decode($from);
        $from = '';
        foreach ($decoded as $part) {
            $from .= $part->text;
        }

        // Use regex para extrair o endereço de e-mail
        if (preg_match('/<(.+)>/', $from, $matches)) {
            return $matches[1];
        }

        // Caso contrário, retorne o texto limpo
        return $from;
    }

    private function getBody($inbox, $email_number)
    {
        $structure = imap_fetchstructure($inbox, $email_number);
        $body = '';

        if (!isset($structure->parts)) {
            $body = imap_body($inbox, $email_number);
            $decodedBody = $this->decodeBody($body, $structure->encoding, $structure->parameters[0]->value ?? 'UTF-8');
            return $this->sanitizeText($decodedBody);
        } else {
            $body = $this->fetchMultipartBody($inbox, $email_number, $structure);
        }

        return $body;
    }

    private function fetchMultipartBody($inbox, $email_number, $structure, $part_number = '')
    {
        $body = '';

        foreach ($structure->parts as $index => $part) {
            $part_number_current = $part_number ? "$part_number." . ($index + 1) : ($index + 1);

            if ($part->type == TYPETEXT) {
                if ($part->subtype == 'PLAIN' || $part->subtype == 'HTML') {
                    $body = imap_fetchbody($inbox, $email_number, $part_number_current);
                    $decodedBody = $this->decodeBody($body, $part->encoding, $part->parameters[0]->value ?? 'UTF-8');
                    return $this->sanitizeText($decodedBody);
                }
            } elseif ($part->type == TYPEMULTIPART) {
                $body = $this->fetchMultipartBody($inbox, $email_number, $part, $part_number_current);
                if (!empty($body)) {
                    return $body;
                }
            }
        }

        return $body;
    }

    private function decodeBody($body, $encoding, $charset = 'UTF-8')
    {
        switch ($encoding) {
            case ENC7BIT:
                return mb_convert_encoding($body, 'UTF-8', $charset);
            case ENC8BIT:
                return mb_convert_encoding(imap_8bit($body), 'UTF-8', $charset);
            case ENCBINARY:
                return imap_binary($body);
            case ENCBASE64:
                return mb_convert_encoding(base64_decode($body), 'UTF-8', $charset);
            case ENCQUOTEDPRINTABLE:
                return mb_convert_encoding(quoted_printable_decode($body), 'UTF-8', $charset);
            case ENCOTHER:
            default:
                return mb_convert_encoding($body, 'UTF-8', $charset);
        }
    }

    private function sanitizeText($text)
    {
        $cleanText = strip_tags($text);
        $cleanText = html_entity_decode($cleanText, ENT_QUOTES, 'UTF-8');
        $cleanText = str_replace("\u{A0}", ' ', $cleanText);
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);
        $cleanText = mb_convert_encoding($cleanText, 'UTF-8', 'auto');

        return $cleanText;
    }
}
