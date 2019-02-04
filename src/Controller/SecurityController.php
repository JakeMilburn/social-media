<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\RegistrationType;
use App\Service\ImgHandler;
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
     * @param ImgHandler $imgHandler
     * @return             \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request, ImgHandler $imgHandler)
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

            if ($user->getProfilePicture() === null) {
                $user->setPath('default-pp.png');
            } else {
                $user->setPath($imgHandler->uploadImage($user));
            }

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


    /**
     * @Route("/profile/password/{id}", name="change_password")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changePassword(Request $request)
    {
        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->getUser();

            //Sets a boolean variable based on if the current password entered is the
            //same as in the db
            $checkPass = $this->encoder->isPasswordValid($user, $data['oldPassword']);

            //If the current password is the same as in the db then update to the new password
            if ($checkPass == true) {

                //Check if the new password is the same as the old password
                if ($this->encoder->isPasswordValid($user, $data['newPassword']) == true) {
                    $this->addFlash('error', 'Your new password must be different to your current password');

                } else {
                    $user->setPassword(
                        $this->encoder->encodePassword($user, $data['newPassword'])
                    );

                    $entityManager = $this->getDoctrine()->getManager();

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Your password has been updated successfully!');

                    return $this->redirectToRoute('profile', ['id' => $user->getId()]);
                }

            } else {
                $this->addFlash('error', 'The current password that you entered is incorrect');
            }

        }

        return $this->render(
            'security/changepasswd.html.twig',
            array(
                'form' => $form->createView(),
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
