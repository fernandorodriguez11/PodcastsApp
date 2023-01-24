<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistroFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class RegistroController extends AbstractController
{
    /**
     * @Route("/registro_usuario", name="registro_usuario") 
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $mensaje = "";

        $usuario = new User();
        
        $form = $this->createForm(RegistroFormType:: class, $usuario);
        $form->handleRequest($request);
        $parameter = $request->request->all();

        if($form->isSubmitted() && $form->isValid()){    

            $em = $this->getDoctrine()->getManager();

            $email = $form['email']->getData();

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                return $this->redirectToRoute("registro_usuario");
            }

            $password = $form['password']->getData();
            $passwordR = $parameter['passwordR'];

            if(!$password === $passwordR){
                $mensaje = "Las contraseñas no coinciden";
                return $this->redirectToRoute("registro_usuario");
            }

            //Codifico la contraseña
            $factory = new PasswordHasherFactory([
                'common' => ['algorithm' => 'bcrypt'],
                'memory-hard' => ['algorithm' => 'sodium'],
            ]);
    
            // Retrieve the right password hasher by its name
            $passwordHasher = $factory->getPasswordHasher('common');
    
            $hash = $passwordHasher->hash($password);

            $usuario->setPassword($hash);
            //$usuario->setPassword($passwordEncoder->encodePassword($usuario, $form['password']->getData()));
    
            $em->persist($usuario);
            $em->flush();

            //Pruebo esto si no utilizo  el addFlash
            $mensaje = "Usuario insertado correctamente";

            return $this->redirectToRoute("registro_usuario");
            /**
             * $this->addFlash('exito', USER::REGISTRO_EXITOSO);
             * return $this->redirectToRoute("registro");
             */
        }
        
        return $this->render('registro/index.html.twig', [
            'titulo_vista' => "Registro de Usuario",
            'formulario' => $form->createView(),
            'mensaje' => $mensaje
        ]);

    }
}
