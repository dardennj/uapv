<?php
namespace Dosi\TheseFondationBundle\Controller\Frontend;
use Dosi\TheseFondationBundle\Entity\Document;
use Dosi\TheseFondationBundle\Form\CandidatType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CandidatController extends Controller
{
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new CandidatType());

        $error = false;

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);



            if ($form->isValid()) {
                $candidat = $form->getData();
                $candidat->setDateCandidature(new \DateTime('now'));
                $em = $this->getDoctrine()->getManager();

                $candidat->getDiplomes()->upload();
                $candidat->getLettreDirThese()->upload();
                $candidat->getLettreDirUnite()->upload();

                $em->persist($candidat);
                $em->flush();
                return $this->redirect($this->generateUrl('frontend_candidat_success'));
            }
            else
            {
                $candidat = $form->getData();

                if(strlen($candidat->getTheseResume())>1400)
                    $error = "Votre résumé doit contenir au maximum 1400 caractères";

                if(strlen($candidat->getTheseAdeq())>1400)
                    $error = "Votre partie adéquation doit contenir au maximum 1400 caractères";

                if(strlen($candidat->getTheseProg())>9800)
                    $error = "Votre programme doit contenir au maximum 9800 caractères";

                if(strlen($candidat->getTheseRetour())>1400)
                    $error = "Votre partie 'Retombée ...' doit contenir au maximum 1400 caractères";

            }
        }
        return $this->render('DosiTheseFondationBundle:Frontend:index.html.twig', array('form' => $form->createView(),'error'=>$error));
    }

    public function successAction(Request $request)
    {
        return $this->render('DosiTheseFondationBundle:Frontend:success.html.twig');
    }
}