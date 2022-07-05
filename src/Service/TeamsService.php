<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class TeamsService
 * @package App\Service
 * service="app.controller.thumbnail"
 */
class TeamsService
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var array|bool|float|int|string|\UnitEnum|null
     */
    private $webPath;

    /**
     * TeamsService constructor.
     * @param ContainerInterface $container
     * @param SessionInterface $session
     */
    public function __construct(ContainerInterface $container, SessionInterface $session)
    {
        $this->container = $container;
        $this->session = $session;
        $this->webPath = $this->container->getParameter('kernel.project_dir');
    }

    /**
     * @return array
     */
    public function getDataFromJson(): array
    {
        $jsonFile = $this->webPath . '/sample_data_test.json';

        $data = trim(file_get_contents($jsonFile));
        $data = json_decode($data, true);
        return $data;
    }

    /**
     * @return array
     */
    public function getCustomTeam(): array
    {
        /* build a Custom team array */
        $data = $this->getDataFromJson();
        $team = [];

        foreach ($data['teams'] as $key => $array) {

            /* Team Level */
            $total_powers = 0;
            foreach ($array as $libelle => $value) {
                $ages = 0;
                if (trim($libelle) != 'members') {
                    $team[$key][$libelle] = $value;
                } else {
                    /* Members Level */
                    foreach ($value as $k => $members) {
                        if (!isset($members['powers']) || !is_array($members['powers'])) {
                            $members['powers'] = [];
                        }
                        $total_powers += sizeof($members['powers']);
                    }
                    foreach ($value as $k => $members) {
                        $ages += $members["age"];
                        if (!isset($members['powers']) || !is_array($members['powers'])) {
                            $members['powers'] = [];
                        }
                        $members['nb_powers_member'] = sizeof($members['powers']);

                        $members['avg_powers_member'] = $members['nb_powers_member'] / $total_powers;
                        $team[$key]['members'][$k] = $members;
                    }

                    $team[$key]['members_nb_team'] = count($value);
                    $team[$key]['avg_ages_team'] = $ages / count($value);

                }

            }
            $team[$key]['avg_powers_team'] = $total_powers / $team[$key]['members_nb_team'];
            $team[$key]['total_powers_team'] = $total_powers;

        }
        return $team;
    }

    /**
     * @return string
     */
    public function getTeamformatCSV(): string
    {
        $header = ['Squad Name', 'HomeTown', 'Formed Year', 'Base', 'Number of members', 'Average Age', 'Average strengh of team', 'Is Active'];
        $keyTeam = ['squadName','homeTown','formed','secretBase','members_nb_team','avg_ages_team','avg_powers_team','active'];
        $string_csv = "";
        $sep = '';
        foreach ($header as $head) {
            $string_csv .= $sep . $head;
            $sep = ';';
        }
        $data = $this->getCustomTeam();
        $string_csv .= "\n";
        foreach ($data as $key => $datateam) {
            $sep = '';
            foreach ($keyTeam as $key) {

                    $string_csv .= $sep . $datateam[$key];
                    $sep = ";";
            }
            $string_csv .= "\n";
        }
        $this->session->set('csvTeam', $string_csv);
        return trim($string_csv);
    }

    /**
     * @return string
     */
    public function getMembersformatCSV(): string
    {
        $mappingFile = $this->webPath . '/mapping.csv';
        $mappingData = $this->getMappingData($mappingFile);
        $data = $this->getCustomTeam();
        $string_csv = "Squad name;Home Town;Name;Secret ID;Age;Number of Power;Average strengh of member;Power1;PowerCode1;Power2;PowerCode2;Power3;PowerCode3;Power4;PowerCode4;Power5;PowerCode5\n";
        foreach ($data as $key => $datateam) {

            foreach ($datateam['members'] as $line => $value) {
                $string_csv .= $datateam['squadName'] . ';' . $datateam['homeTown'] . ';';
                $string_csv .= $value['name'] . ';' . $value['secretIdentity'] . ';' . $value['age'] . ';' . $value['nb_powers_member'] . ';' . $value['avg_powers_member'] . ";";
                if ($value['nb_powers_member'] > 0) {
                    $sep = '';
                    foreach ($value['powers'] as $k => $val) {
                        $string_csv .= $sep . $val . ";" . $mappingData["$val"];
                        $sep = ';';
                    }
                }
                $string_csv .= "\n";
            }
        }
        $this->session->set('csvMermbers', $string_csv);
        return trim($string_csv);
    }

    /**
     * @param $file
     * @return array
     */
    public function getMappingData($file): array
    {
        $mapping = [];
        if (($fp = fopen("$file", "r")) !== FALSE) {
            while (($row = fgetcsv($fp, 1000, ";")) !== FALSE) {
                $mapping [trim($row[0])] = $row[1];
            }
            fclose($fp);
        }
        return $mapping;
    }
}