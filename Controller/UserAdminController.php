<?php
namespace Sonata\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\Datagrid\ORM\ProxyQuery;

class UserAdminController extends Controller
{

  protected function getPageLink()
  {
    $httpHost = $this->container->get('request')->getHttpHost();
    $baseUrl = $this->container->get('request')->getBaseUrl();
    return 'http://' . $httpHost . $baseUrl;
  }

  public function sendAccountEnabledEmail($toAddress)
  {
    $applicationTitle = $this->container->get('adminSettings')->adminTitle;  
      
    $message = \Swift_Message::newInstance()
            ->setSubject($applicationTitle.' - Account Approved')
            ->setFrom($this->container->getParameter('fos_user.registration.confirmation.from_email'))
            ->setTo($toAddress)
            ->setContentType('text/html')
            ->setBody('<html>
               Your '.$applicationTitle.' account has been approved.<br/>
               You can now log in.<br/>
               <a href="' . $this->getPageLink().'">'.$this->getPageLink().'</a></html>')
    ;
    $this->get('mailer')->send($message);
  }

  public function batchActionEnable($query)
  {
    $em = $this->getDoctrine()->getEntityManager();

    foreach($query->getQuery()->iterate() as $pos => $object)
    {
      $object[0]->setEnabled('1');

      $this->sendAccountEnabledEmail($object[0]->getEmail());
    }

    $em->flush();
    $em->clear();

    $this->getRequest()->getSession()->setFlash('sonata_flash_success', 'The selected users have been approved');

    return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
  }

  public function batchActionDisable($query)
  {
    $em = $this->getDoctrine()->getEntityManager();

    foreach($query->getQuery()->iterate() as $pos => $object)
    {
      $object[0]->setEnabled('0');
    }

    $em->flush();
    $em->clear();

    $this->getRequest()->getSession()->setFlash('sonata_flash_success', 'The selected users have been unapproved');

    return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
  }

  public function enableAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();

    $userManager = $this->container->get('fos_user.user_manager');
    $user = $userManager->findUserBy(array("id" => $id));
    $user->setEnabled('1');

    $em->flush();
    $em->clear();

    $this->sendAccountEnabledEmail($user->getEmail());

    $this->getRequest()->getSession()->setFlash('sonata_flash_success', $user->getEmail() . ' has been approved');

    return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
  }

  public function disableAction($id)
  {
    $em = $this->getDoctrine()->getEntityManager();

    $userManager = $this->container->get('fos_user.user_manager');
    $user = $userManager->findUserBy(array("id" => $id));
    $user->setEnabled('0');

    $em->flush();
    $em->clear();

    $this->getRequest()->getSession()->setFlash('sonata_flash_success', $user->getEmail() . ' has been unapproved');


    return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
  }

}