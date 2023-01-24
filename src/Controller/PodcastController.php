<?php

namespace App\Controller;

use App\Entity\Podcasts;
use App\Entity\User;
use App\Form\PodcastFormType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

use Knp\Component\Pager\PaginatorInterface;

class PodcastController extends AbstractController
{


    /**
     * @Route("/podcast/{id}", name="crear_podcast")
     */
    public function index($id, Request $request, SluggerInterface $slugger): Response
    {

        $podcast = new Podcasts();
        $usuario = new User();

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(User::class)->find($id);

        $form = $this->createForm(PodcastFormType:: class, $podcast);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            $podcast->setAutor($usuario);

            $imagen = $form['imagen']->getData();
            if($imagen){
            
                $nombreCodificado = uniqid().'.'.$imagen->guessExtension();
    
                try{
                    $imagen->move(
                        $this->getParameter('images_directory'), $nombreCodificado
                    );
                }
                catch(FileException $e){
                    throw new \Exception("Upsss ha ocurrido un error");
                }
    
                $podcast->setImagen($nombreCodificado);
            }

            $audio = $form['audio']->getData();
            if($audio){
            
                $nombreCodificado = uniqid().'.'.$audio->guessExtension();
    
                try{
                    $audio->move(
                        $this->getParameter('audios_directory'), $nombreCodificado
                    );
                }
                catch(FileException $e){
                    throw new \Exception("Upsss ha ocurrido un error");
                }
    
                $podcast->setAudio($nombreCodificado);
            }

            $em->persist($podcast);
            $em->flush();
        }

        return $this->render('podcast/crear.html.twig', [
            'controller_name' => 'Crear Podcast',
            'formulario' => $form->createView(),
            'usuario' => $usuario,
            'uno' => '',
            'dos' => '',
            'tres' => 'active'
        ]);
    }

    /**
     * @Route("/explorar/{id}", name="explorar_podcast")
     */
    public function explorar($id, Request $request, PaginatorInterface $paginator): Response
    {

        $podcasts = new Podcasts();
        $usuario = new User();

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(User::class)->find($id);

        $podcasts_query = $em->getRepository(Podcasts::class)->allPodcasts();

        $podcasts = $paginator->paginate(
            $podcasts_query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        return $this->render('podcast/explorar.html.twig', [
            'controller_name' => 'Explorar Podcast',
            'usuario' => $usuario,
            'podcasts' => $podcasts,
            'uno' => '',
            'dos' => 'active',
            'tres' => '',
        ]);
    }

    /**
     * @Route("/ver_podcast/{id}/{idu}", name="ver_podcast")
     */
    public function ver_podcast( $id, $idu, Request $request): Response
    {

        $podcast = new Podcasts();
        $usuario = new User();
        

        $em = $this->getDoctrine()->getManager();
        $podcast = $em->getRepository(Podcasts::class)->find($id);
        $usuario = $em->getRepository(User::class)->find($idu);

        $form = $this->createForm(PodcastFormType:: class, $podcast);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $imagen = $form['imagen']->getData();
            if($imagen){
            
                $nombreCodificado = uniqid().'.'.$imagen->guessExtension();
    
                try{
                    $imagen->move(
                        $this->getParameter('images_directory'), $nombreCodificado
                    );
                }
                catch(FileException $e){
                    throw new \Exception("Upsss ha ocurrido un error");
                }
    
                $podcast->setImagen($nombreCodificado);
            }

            $audio = $form['audio']->getData();
            if($audio){
            
                $nombreCodificado = uniqid().'.'.$audio->guessExtension();
    
                try{
                    $audio->move(
                        $this->getParameter('audios_directory'), $nombreCodificado
                    );
                }
                catch(FileException $e){
                    throw new \Exception("Upsss ha ocurrido un error");
                }
    
                $podcast->setAudio($nombreCodificado);
            }
            
            $em->persist($podcast);
            $em->flush();

        }

        return $this->render('podcast/verPodcast.html.twig', [
            'controller_name' => 'Ver Podcast',
            'usuario' => $usuario,
            'podcast' => $podcast,
            'formulario' => $form->createView(),
            'uno' => '',
            'dos' => '',
            'tres' => '',
        ]);

    }

    /**
     * @Route("/eliminar_podcast/{id_podcast}/{id}", name="eliminar_podcast")
     */
    public function eliminar_podcast($id_podcast, $id, Request $request): Response
    {

        $podcast = new Podcasts();

        $em = $this->getDoctrine()->getManager();
        $podcast = $em->getRepository(Podcasts::class)->find($id_podcast);

        $em->remove($podcast);
        $em->flush();

        return $this->redirectToRoute("explorar_podcast", ['id' => $id]);
        
    }
}