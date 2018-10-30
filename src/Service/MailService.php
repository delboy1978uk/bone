<?php

namespace Bone\Service;

use Zend\Mail;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Message;
use Zend\Mime\Part;

class MailService
{
    /** @var  Mail\Message */
    private $mail;
    /** @var  Mail\Transport\TransportInterface */
    private $transport;

    private $header;
    private $footer;

    private $message;
    private $from;
    private $to;
    private $subject;


    /**
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        $this->mail = new Mail\Message();

        $this->transport = isset($config['transport']) ? $config['transport'] : new Mail\Transport\Sendmail();
        isset($config['from']) ? $this->mail->setTo($config['from']) : null;
        isset($config['to']) ? $this->mail->setTo($config['to']) : null;
        isset($config['subject']) ? $this->mail->setTo($config['subject']) : null;
        isset($config['message']) ? $this->mail->setTo($config['message']) : null;
    }

    /**
     * @param TransportInterface $transport
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @return bool
     */
    public function send()
    {
        $msg = new Part($this->header.$this->message.$this->footer);
        $msg->type = 'text/html';
        $mime = new Message();
        $mime->setParts([$msg]);
        $this->mail->setBody($mime)
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setSubject($this->subject);

        $this->transport->send($this->mail);
        return true;
    }

    /**
     * @param string $header
     * @return MailService
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @param string $footer
     * @return MailService
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @param $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }
}