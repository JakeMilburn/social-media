<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use http\Env\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * @package App\Controller
 */
class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="login")
     * @param           Request $request
     * @param           AuthenticationUtils $utils
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();

        $lastUsername = $utils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            [
                'error' => $error,
                'last_username' => $lastUsername,
            ]
        );
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }

    private $encoder;

    /**
     * SecurityController constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Route("/register", name="register")
     * @param              Request $request
     * @return             \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $user->setPassword(
                $this->encoder->encodePassword($user, $user->getPassword())
            );

            $user->setRoles(['ROLE_USER']);

            $entityManager = $this->getDoctrine()->getManager();
            $user->uploadImage();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('feed');
        }

        return $this->render(
            'security/register.html.twig',
            array(
                'form' => $form->createview(),
            )
        );
    }

//  /**
//   * @Route("/manage", name="manage")
//   * @Method({"GET"})
//   * @param            Request $request
//   * @return           \Symfony\Component\HttpFoundation\Response
//   */
//  public function manage(Request $request)
//  {
//    $users = $this->getDoctrine()->getRepository(User::class)->findAll();
//
//    return $this->render(
//      'security/manage.html.twig',
//      array
//      (
//        'users' => $users,
//      )
//    );
//  }
//
//
//  /**
//   * @Route("/perms", name="perms")
//   * @Method({"GET"})
//   */
//  public function permissions()
//  {
//
//    $users = $this->getDoctrine()->getRepository(User::class)->findAll();
//    $entityManager = $this->getDoctrine()->getManager();
//
//    foreach ($users as $user) {
//      $id = $user->getId();
//      if (array_key_exists($id, $_POST)) {
//        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
//      } else {
//        $user->setRoles(['ROLE_USER']);
//      }
//
//      $entityManager->persist($user);
//    }
//        $entityManager->flush();
//
//        return $this->redirectToRoute('sweet_list');
//    }


}
