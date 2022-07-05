<?php

namespace App\Controller;

use App\Service\TeamsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class TeamsController extends AbstractController
{
    /**
     * @Route("/teams", name="app_teams")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('teams/index.html.twig', [
        ]);

    }

    /**
     * @Route("/teams/csv", name="app_teams_csv")
     * @param Session $session
     * @param Request $request
     * @return Response
     */
    public function getCsv(Session $session, Request $request): Response
    {
        $dataCSV = [];
        $file = '';
        if ($request->query->get('team')) {
            $dataCSV = $session->get('csvTeam');
            $file = "teams";
        } else if ($request->query->get('members')) {
            $dataCSV = $session->get('csvMermbers');
            $file = "team_members";
        }
        return new Response(
            $dataCSV ? $dataCSV : [],
            200,
            [
                'Content-Type' => 'application/csv',
                "Content-disposition" => "attachment; filename=" . $file . ".csv"
            ]
        );
    }
}
