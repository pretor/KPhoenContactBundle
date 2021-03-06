<?php

namespace KPhoen\ContactBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ContactControllerTest extends WebTestCase
{
    public function testContact()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            1, $crawler->filter('h2:contains("Contact")')->count()
        );

        $form = $crawler->selectButton('kphoen_contact_submit')->form();
        $form->setValues(array(
            'contact_message[sender_name]' => 'Joe',
            'contact_message[sender_mail]' => 'joe@joe.fr',
        ));

        // submit the form
        $crawler = $client->submit($form);

        // there is an error so the user is not redirected
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotInstanceOf('Symfony\\Component\\HttpFoundation\\RedirectResponse', $client->getResponse());

        // no mail was sent
        $collector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(0, $collector->getMessageCount());

        $form = $crawler->selectButton('kphoen_contact_submit')->form();
        $form->setValues(array(
            'contact_message[sender_name]'  => 'Joe',
            'contact_message[sender_mail]'  => 'joe@joe.fr',
            'contact_message[subject]'      => 'test subject',
            'contact_message[content]'      => 'test content !',
        ));

        $crawler = $client->submit($form);

        // this time, as there is no error, the user is redirected
        $this->assertTrue($client->getResponse()->isRedirect('/contact'), 'The user is redirected to the right place');

        // a mail was sent
        $collector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());

        // the mail is well-formed
        list($mail) = $collector->getMessages();

        $this->assertTrue($mail instanceof \Swift_Message);
        $this->assertContains('test content !', (string) $mail);
        $this->assertEquals(array('foo@bar.baz' => ''), $mail->getTo());
        $this->assertEquals(array('no-reply@bar.baz' => ''), $mail->getFrom());
        $this->assertEquals(array('joe@joe.fr' => 'Joe'), $mail->getReplyTo());
        $this->assertEquals('test subject', $mail->getSubject());
    }
}
