<?php

namespace Reg2005\LaravelEmailDatabaseLog;

use Carbon\Carbon;
use DB;
use Illuminate\Mail\Events\MessageSending;

class EmailLogger
{
    /**
     * Handle the event.
     *
     * @param MessageSending $event
     */
    public function handle(MessageSending $event)
    {
        $message = $event->message;


        DB::table('email_log')->insert([
            'to' => !$message->getHeaders()->get('To') ? null : $message->getHeaders()->get('To')->getFieldBody(),
			'bcc' => !$message->getHeaders()->get('Bcc') ? null : $message->getHeaders()->get('Bcc')->getFieldBody(),
            'subject' => $message->getSubject(),
            'body' => $message->getBody(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Get a loggable string out of a Swiftmailer entity.
     *
     * @param  \Swift_Mime_MimeEntity $entity
     * @return string
     */
    protected function getMimeEntityString(\Swift_Mime_MimeEntity $entity)
    {
        $string = (string) $entity->getHeaders().PHP_EOL.$entity->getBody();

        foreach ($entity->getChildren() as $children) {
            $string .= PHP_EOL.PHP_EOL.$this->getMimeEntityString($children);
        }

        return $string;
    }
}
