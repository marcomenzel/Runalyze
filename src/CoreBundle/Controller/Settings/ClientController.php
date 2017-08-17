<?php

namespace Runalyze\Bundle\CoreBundle\Controller\Settings;

use Runalyze\Bundle\CoreBundle\Entity\AccountRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Runalyze\Bundle\CoreBundle\Entity\Account;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ClientController extends Controller
{
    /**
     * @return AccountRepository
     */
    protected function getAccountRepository()
    {
        return $this->getDoctrine()->getRepository('CoreBundle:Account');
    }

    /**
     * @Route("/settings/services", name="settings-services")
     * @Security("has_role('ROLE_USER')")
     */
    public function settingsAccountAction(Request $request, Account $account)
    {
        return $this->render('account/services.html.twig', [
        ]);
    }
}
