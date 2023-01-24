<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Podcasts;
use App\Form\UsuarioFormType;
use App\Form\RegistroFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class HomeController extends AbstractController
{
    /**
     * @Route("/home/{id}", name="home")
     */
    public function index($id, Request $request): Response
    {
        $usu = $this->getUser();
        $usuario = new User();
        $em = $this->getDoctrine()->getManager(); 
        $usuario = $em->getRepository(User::class)->find($id);
        
        if(!empty($usuario)){

            $podcasts = new Podcasts();
            $podcasts = $em->getRepository(Podcasts::class)->findBy(['autor' => $id]);

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'usuario' => $usuario,
                'podcasts' => $podcasts,
                'uno' => 'active',
                'dos' => '',
                'tres' => ''
            ]);
        }else{
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request): Response
    {
 
        $usuario = new User();

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        // Retrieve the right password hasher by its name
        $passwordHasher = $factory->getPasswordHasher('common');

        $form = $this->createForm(UsuarioFormType:: class, $usuario);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();  

            $email = $form['email']->getData();;
            $password = $form['password']->getData();

            $usuario = $em->getRepository(User::class)->findBy(['email' => $email]);
    
            if(!empty($usuario)){
                
                foreach($usuario as $usu){
                   
                    $password_bd = $usu->getPassword();

                    if($passwordHasher->verify($password_bd, $password)){
                        
                        print($this->getUser($usuario));
                        return $this->redirectToRoute('home', ['id'=> $usu->getId()]);    
                    }  
                }
            
            }else{
                return $this->redirectToRoute('login');
            }

        }

        return $this->render('security/loginUsuario.html.twig', [
            'controller_name' => 'Login',
            'formulario' => $form->createView(),
        ]);
       
    }

    /**
     * @Route("/perfil/{id}", name="editar_perfil")
     */
    public function editar_perfil($id, Request $request): Response
    {
 
        $usuario = new User();

        $em = $this->getDoctrine()->getManager();  
        $usuario = $em->getRepository(User::class)->find($id);

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        // Retrieve the right password hasher by its name
        $passwordHasher = $factory->getPasswordHasher('common');

        $form = $this->createForm(RegistroFormType:: class, $usuario);
        $form->handleRequest($request);
        $parameter = $request->request->all();

        if($form->isSubmitted() && $form->isValid()){

            $email = $form['email']->getData();
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                return $this->redirectToRoute("editar_perfil");
            }

            $password = $formu['password']->getData();
            if($password != $usuario->getPassword()){
                $passwordR = $parameter['passwordR'];

                if(!$password === $passwordR){
                    $mensaje = "Las contraseñas no coinciden";
                    return $this->redirectToRoute("editar_todo", ['id' => $usuario->getId(), 'idp' => $idp]);
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
            }
    
            $em->persist($usuario);
            $em->flush();

        }

        return $this->render('home/perfil.html.twig', [
            'controller_name' => 'Perfil',
            'usuario' => $usuario,
            'formulario' => $form->createView(),
            'uno' => '',
            'dos' => '',
            'tres' => ''
        ]);
       
    }

}
