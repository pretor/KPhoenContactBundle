<?php
namespace KPhoen\ContactBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sender_name', null, array(
            'label' => 'contact.form.sender_name'
        ));
        $builder->add('sender_mail', 'email', array(
            'label' => 'contact.form.sender_mail'
        ));
        $builder->add('subject', null, array(
            'label' => 'contact.form.subject'
        ));
        $builder->add('content', 'textarea', array(
            'label' => 'contact.form.content'
        ));
    }

    public function getName()
    {
        return 'message';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'KPhoen\ContactBundle\Model\Message',
        );
    }
}
