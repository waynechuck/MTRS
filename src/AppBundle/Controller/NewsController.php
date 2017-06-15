<?php
/**
 * Created by PhpStorm.
 * User: Michael Trotzer
 * Date: 01.06.2017
 * Time: 00:30
 */

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Include everything for the Forms
 */

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

//@TODO Klassenname groß
class NewsController extends Controller
{

    public function anzeigenAction()
    {
        $news = $this->getDoctrine()
            ->getRepository('AppBundle:News')
            ->findAll();

        //@TODO die gibt es nicht
        return $this->render('news/anzeigen.html.twig', [
            'news' => $news
        ]);
    }

    public function erstellenAction(Request $request)
    {
        /**
        * @TODO Der richtige weg wäre:
        * - @EntityManager holen
        * - @EntityManager::getRepository gibt uns die Klasse
        */

        $news = new news;

        $form =$this->createFormBuilder($news)
            //TextType
            ->add('artikelname', TextType::class, ['attr' => ['class' => 'form-control', 'style' =>'margin-bottom:15px']])
            ->add('artikel', TextareaType::class, ['attr' => ['class' => 'form-control', 'style' =>'margin-bottom:15px']])
            ->add('autor', TextType::class, ['attr' => ['class' => 'form-control', 'style' =>'margin-bottom:15px']])

            //DateType
            ->add('veroeffentlichungsdatum', DateType::class, [
                'placeholder' => [
                    'year' => 'Jahr',
                    'month' => 'Monat',
                    'day' => 'Tag'],
                'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']])

            //SubmitType
            ->add('save', SubmitType::class, ['label' => 'News veröffentlichen', 'attr' => ['class' => 'btn btn-primary', 'style' =>'margin-bottom:15px']])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //getData
            $artikelname = $form['artikelname']->getData();
            $artikel = $form['artikel']->getData();
            $autor = $form['autor']->getData();
            $veroeffentlichungsdatum = $form['veroeffentlichungsdatum']->getData();



            $news->setArtikelname($artikelname);
            $news->setArtikel($artikel);
            $news->setAutor($autor);
            $news->setVeroeffentlichungsdatum($veroeffentlichungsdatum);


            $em = $this->getDoctrine()->getManager();

            $em->persist($news);
            $em->flush();

            $this->addFlash(
                'notice',
                'News veröfffentlicht!'
            );

            //@TODO nur ein return pro Methode
            //@TODO die Route gibt es nicht
            return $this->redirectToRoute('News_anzeigen');
        }

        return $this->render('news/erstellen.html.twig', [
            'form' => $form->createView()
        ]);
    }
}