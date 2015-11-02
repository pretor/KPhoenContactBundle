<?php

namespace KPhoen\ContactBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ContactController extends BaseContactController
{
    /**
     * @Template()
     */
    public function contactAction(Request $request)
    {
        list($message, $form) = $this->getContactForm();
        
            if ($request->isMethod('POST') && $request->query->get('ajax')) {
            $res = $this->handleContactForm($request, $form, $message);
            if($res == null){
                return new Response('fail');
            }
            else{
                return new Response('success');
            }
          }

        if ($request->isMethod('POST') && ($res = $this->handleContactForm($request, $form, $message)) !== null) {
            return $res;
        }

        return array(
            'form'          => $form->createView(),
            'route'         => 'contact_send',
            'route_args'    => array(),
        );
    }
}
