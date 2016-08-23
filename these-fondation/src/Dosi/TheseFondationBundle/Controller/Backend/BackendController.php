<?php
namespace Dosi\TheseFondationBundle\Controller\Backend;

use Dosi\TheseFondationBundle\Entity\Candidat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;


class BackendController extends Controller
{
    public function indexAction()
    {
        return $this->render('DosiTheseFondationBundle:Backend:index.html.twig',array());
    }

    public function candidatAction()
    {
        $source = new Entity('DosiTheseFondationBundle:Candidat');
        $grid = $this->get('grid');

        $grid->setSource($source);
        $grid->hideColumns("id");
        $grid->setDefaultOrder('nom', 'asc');

        $grid->setLimits(array(20, 50, 100));

        $grid->setPage(1);

        $actionsColumn = new ActionsColumn('info_column_10', 'Actions');
        $grid->addColumn($actionsColumn, 10);

        // Attach a rowAction to the Actions Column
        $rowAction = new RowAction('Voir', 'backend_candidat_view');
        $rowAction->setColumn('info_column_10');
        $grid->addRowAction($rowAction);

        $grid->isReadyForRedirect();

        $request = $this->get('request');

        return $grid->getGridResponse($request->isXmlHttpRequest() ? 'DosiTheseFondationBundle:Backend:grid.html.twig' : 'DosiTheseFondationBundle:Backend:candidats.html.twig');
    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $candidat = $em->getRepository('DosiTheseFondationBundle:Candidat')->find($id);

        return $this->render('DosiTheseFondationBundle:Backend:view.html.twig',array('candidat'=>$candidat));
    }

    public function documentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('DosiTheseFondationBundle:Document')->find($id);

        $filePath = $document->getAbsolutePath().'/'.$document->getHiddenName();


        header('Content-Description: File Transfer');
        header('Content-Type:'.mime_content_type($filePath));
        header('Content-Disposition: inline; filename=' . $document->getName());
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        @ob_clean();
        flush();

        readfile($filePath);
        die;
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $candidat = $em->getRepository('DosiTheseFondationBundle:Candidat')->find($id);

        if (!$candidat) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }


        $em->remove($candidat);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_candidat'));
    }

    public function histoAction()
    {
        $request = $this->get('request');
        $id = $request->query->get('id');

        $em = $this->getDoctrine()->getManager();
        $now = new \DateTime('now');

        $begin =  new \DateTime('06/15/2015');
        $end = $now;


        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);

        $output = array();
        $candidats = array();

        foreach ($period as $dt)
        {
            $nb_cand = $em->getRepository('DosiTheseFondationBundle:Candidat')->getCandidatures($dt);
            $candidats[] = array($dt->getTimeStamp()*1000,floatval($nb_cand));
        }

        $output['candidats'] = $candidats;
        $output['title'] = 'Historique des candidatures';

        echo json_encode($output);
        die;
    }

    public function exportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $candidats = $em->getRepository('DosiTheseFondationBundle:Candidat')->findAll();

        $titles= array('Date','Bourse Pierre Bergé','Bourse Jean-Henri Fabre','Nom','Prénom','Adresse','Téléphone','Mail','Titre de la Thèse','Résumé','Adéquation','Grandes lignes du programme','Retombée','ED','Unité de Recherche','Directeur de thèse');

        $content = '<table border=1>';
        $content .= '<tr>';
        foreach ($titles as $title) {
            $content .= sprintf("<th>%s</th>", htmlentities($title, ENT_QUOTES));
        }
        $content .= '</tr>';

        foreach($candidats as $candidat)
        {
            $content .= '<tr>';
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getDateCandidature()->format( 'd-m-Y' ), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getThesePB(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getTheseJHF(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getNom(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getPrenom(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getAdresse(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getTel(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getEmail(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getTheseTitre(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getTheseResume(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getTheseAdeq(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getTheseProg(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getTheseRetour(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getTheseStruct(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getTheseUnite(), ENT_QUOTES));
            $content .= sprintf("<td>%s</td>", htmlentities($candidat->getTheseDirecteur(), ENT_QUOTES));
            $content .= '</tr>';
        }
        $content .= '</table>';

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: inline; filename=these-fondation.xls");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($content));

        @ob_clean();
        flush();

        echo $content;
    }
}