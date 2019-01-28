<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LoginCidadao\PhoneVerificationBundle\Controller;

use LoginCidadao\CoreBundle\Helper\GridHelper;
use LoginCidadao\PhoneVerificationBundle\Entity\BlockedPhoneNumber;
use LoginCidadao\PhoneVerificationBundle\Entity\BlockedPhoneNumberRepository;
use LoginCidadao\PhoneVerificationBundle\Form\BlockPhoneFormType;
use LoginCidadao\PhoneVerificationBundle\Form\SearchPhoneNumberType;
use LoginCidadao\PhoneVerificationBundle\Model\BlockPhoneNumberRequest;
use LoginCidadao\PhoneVerificationBundle\Service\BlocklistInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class AdminBlocklistController
 * @package LoginCidadao\PhoneVerificationBundle\Controller
 *
 * @Security("has_role('ROLE_EDIT_BLOCKED_PHONES')")
 * @codeCoverageIgnore
 */
class AdminBlocklistController extends Controller
{
    /**
     * @Route("/admin/phones/blocklist", name="phone_blocklist_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $form = $this->createForm(SearchPhoneNumberType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $grid = new GridHelper();
            $grid->setId('person-grid');
            $grid->setPerPage(5);
            $grid->setMaxResult(5);
            $grid->setInfiniteGrid(true);
            $grid->setRoute('lc_admin_person_grid');
            $grid->setRouteParams([$form->getName()]);

            if ($data['phone']) {
                /** @var BlockedPhoneNumberRepository $blockedPhoneRepo */
                $blockedPhoneRepo = $this->getDoctrine()->getRepository(BlockedPhoneNumber::class);
                $query = $blockedPhoneRepo->getSearchByPartialPhoneQuery($data['phone']);
                $grid->setQueryBuilder($query);
            }

            $gridView = $grid->createView($request);
        }

        return [
            'form' => $form->createView(),
            'grid' => $gridView ?? null,
        ];
    }

    /**
     * @Route("/admin/phones/blocklist/new", name="phone_blocklist_new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $blockRequest = new BlockPhoneNumberRequest($this->getUser());

        $blockedPhones = [];
        $form = $this->createForm(BlockPhoneFormType::class, $blockRequest);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $phoneNumber = $blockRequest->phoneNumber;

            $blocklist = $this->getBlocklistService();
            $blockedPhoneNumber = $blocklist->addBlockedPhoneNumber($phoneNumber, $blockRequest->getBlockedBy());
            $blockedPhones = $blocklist->checkPhoneNumber($blockedPhoneNumber->getPhoneNumber());

            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('success', "Phone number successfully banned.");
            if (($count = count($blockedPhones)) > 0) {
                $session->getFlashBag()->add('success', "{$count} accounts were blocked blocked.");
            }

            return $this->redirectToRoute('phone_blocklist_list');
        }

        return [
            'form' => $form->createView(),
            'blockedPhones' => $blockedPhones,
        ];
    }

    /**
     * @return BlocklistInterface
     */
    private function getBlocklistService(): BlocklistInterface
    {
        /** @var BlocklistInterface $blocklistService */
        $blocklistService = $this->get('phone_verification.blocklist');

        return $blocklistService;
    }
}
