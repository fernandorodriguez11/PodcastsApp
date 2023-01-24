<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Podcasts;
use App\Form\PodcastFormType;
use App\Form\RegistroFormType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Knp\Component\Pager\PaginatorInterface;

class AdminController extends AbstractController
{

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(PaginatorInterface $paginator, Request $request): Response
    {   
        $user = $this->getUser();
        $usuarios_podcast = new User();

        if(!$user){
            return $this->redirectToRoute('login');
        }else{

            $em = $this->getDoctrine()->getManager(); 
            $query= $em->getRepository(User::class)->todo();

            $usuarios_podcast = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                13 /*limit per page*/
            );

            return $this->render('admin/dashboard.html.twig', [
                'controller_name' => 'Inicio de Sesión',
                'usuarios' => $usuarios_podcast,
                
            ]);
        }
        
    }

     /**
     * @Route("/editar2/{id_podcast}", name="editar2_todo")
     */
    public function editar2($id_podcast, Request $request): Response
    {   
        $user = $this->getUser();
        $podcasts = new Podcasts();

        if(!$user){
            return $this->redirectToRoute('login');
        }else{



            $em = $this->getDoctrine()->getManager(); 
            $podcasts = $em->getRepository(Podcasts::class)->podcastUsuario($id_podcast);
            
            return $this->render('admin/editarUP.html.twig', [
                'controller_name' => 'Editar Usuario y Podcast',
                'usuarios_podcast' => $podcasts,
                
            ]);
        }
        
    }


     /**
     * @Route("/editar/{id}/{idp}", name="editar_todo")
     */
    public function editar(User $usuario, $idp , EntityManagerInterface $em, Request $request): Response
    {   
        $user = $this->getUser();
        

        if(!$user){
            return $this->redirectToRoute('login');
        }else{
            $podcasts = new Podcasts();
            $em = $this->getDoctrine()->getManager(); 
      
            if($idp != 0){

                $podcasts = $em->getRepository(Podcasts::class)->find($idp);
            }

            $formp = $this->createForm(PodcastFormType:: class, $podcasts);
            $formp->handleRequest($request);
            $parameter = $request->request->all();

            if($formp->isSubmitted() && $formp->isValid()){
                
                $imagen = $formp['imagen']->getData();
                
                if(!empty($imagen)){
                
                    $nombreCodificado = uniqid().'.'.$imagen->guessExtension();
        
                    try{
                        $imagen->move(
                            $this->getParameter('images_directory'), $nombreCodificado
                        );
                    }
                    catch(FileException $e){
                        throw new \Exception("Upsss ha ocurrido un error");
                    }
        
                    $podcasts->setImagen($nombreCodificado);
                }
    
                $audio = $formp['audio']->getData();
                if(!empty($audio)){
                
                    $nombreCodificado = uniqid().'.'.$audio->guessExtension();
        
                    try{
                        $audio->move(
                            $this->getParameter('audios_directory'), $nombreCodificado
                        );
                    }
                    catch(FileException $e){
                        throw new \Exception("Upsss ha ocurrido un error");
                    }
        
                    $podcasts->setAudio($nombreCodificado);
                }
                $em->persist($podcasts);
                $em->flush();

                return $this->redirectToRoute("editar_todo", ['id' => $usuario->getId(), 'idp' => $idp]);
            }

            $formu = $this->createForm(RegistroFormType:: class, $usuario);
            $formu->handleRequest($request);
            
            if($formu->isSubmitted() && $formu->isValid()){
                
                $email = $formu['email']->getData();

                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    return $this->redirectToRoute("editar_todo", ['id' => $usuario->getId(), 'idp' => $idp]);
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

                return $this->redirectToRoute("editar_todo", ['id' => $usuario->getId(), 'idp' => $idp]);

            }

            return $this->render('admin/editarUP.html.twig', [
                'controller_name' => 'Editar Usuario y Podcast',
                'podcast' => $podcasts,
                'usuario' => $usuario,
                'formulariop' => $formp->createView(),
                'formulariou' => $formu->createView(),
                
            ]);
        }
        
    }

    /**
     * @Route("/delete/{id}", name="delete_podcast")
     */
    public function delete_podcast(EntityManagerInterface $em, Podcasts $podcast){

        $em->remove($podcast);
        $em->flush();

        return $this->redirectToRoute('dashboard');
    }
    
    /**
     * @Route("/deleteUsuario/{id}", name="delete_usuario")
     */
    public function delete_usuario(EntityManagerInterface $em, User $usuario){

        $em->remove($usuario);
        $em->flush();

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/deleteTodo/{id}", name="delete_todo")
     */
    public function delete_todo(EntityManagerInterface $em, User $usuario){

        $podcasts = new Podcasts();
        

        $em = $this->getDoctrine()->getManager(); 
        $podcasts = $em->getRepository(Podcasts::class)->findBy(['autor' => $usuario->getId()]);
        
        foreach($podcasts as $podcast){
            $em->remove($podcast);
        }
        
        $em->remove($usuario);
        $em->flush();

        return $this->redirectToRoute('dashboard');
    }
}
