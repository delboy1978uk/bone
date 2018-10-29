<?php

use Bone\Service\MailService;
use Codeception\TestCase\Test;
use Zend\Mail\Transport\TransportInterface;

class MailServiceTest extends Test
{
    public function testCanSendMail()
    {
        $mail = new MailService();
        /** @var TransportInterface $transport */
        $transport = $this->createMock(TransportInterface::class);

        $mail->setTo('test@test.com');
        $mail->setFrom('from@home.com');
        $mail->setSubject('test');
        $mail->setHeader('top');
        $mail->setFooter('bottom');
        $mail->setMessage('hello');
        $mail->setTransport($transport);

        $this->assertTrue($mail->send());
    }
}