<?php

declare(strict_types=1);

namespace Thomasaubert\OssMarvelLibrary;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;


class Api
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getAllCharacters(): array
    {
        $response = $this->client->request(
            'GET',
            'https://www.marvel-cineverse.fr/pages/mcu/encyclopedie/personnages'
        );

        $content = $response->getContent();
        $crawler = new Crawler($content);

        $img = $crawler->filter('.encyclo-vignette img')->each(function (Crawler $node, $i) {
            return $node->attr('src');
        });

        $link = $crawler->filter('.encyclo-vignette a')->each(function (Crawler $node, $i) {
            return $node->attr('href');
        });

        $name = $crawler->filter('.encyclo-vignette .name')->each(function (Crawler $node, $i) {
            return $node->text();
        });

        $characters = [];

        for ($i = 0; $i < count($name); $i++) {
            $characters[] = new Character(
                $name[$i] ,
                $img[$i],
                $link[$i],
            );
        }

        return $characters;
    }

    public function getInfosOfCharacter(string $name, string $link): array {
        $response = $this->client->request(
            'GET',
            $link
        );

        $infos = $response->getContent();

        $crawler = new Crawler($infos);
        $real_name = $crawler->filter('#tab-perso > table > tbody > tr:nth-child(3) > td:nth-child(2)')->each(function (Crawler $node, $i) {
            return $node->text();
        });

        $nationality = $crawler->filter('#tab-perso > table > tbody > tr:nth-child(6) > td:nth-child(2) > a:nth-child(2)')->each(function (Crawler $node, $i) {
            return $node->text();
        });

        $infos_character = [];
        $infos_character[] = new Infos(
            $name,
            $real_name[0],
            $nationality[0],
        );

        return $infos_character;

    }
}




class Character 
{

    private string $name;
    private string $img;
    private string $link;

    public function __construct(string $name, string $img, string $link) {
        $this->name = $name;
        $this->img = $img;
        $this->link = $link;

    }
    

    public function getName(): string {
        return $this->name;
    }

    public function getImg(): string {
        return $this->img;
    }

    public function getLink(): string {
        return $this->link;
    }

}

class Infos
{
    private string $name;
    private string $real_name;
    private string $nationality;

    public function __construct(string $name, string $real_name, string $nationality) {
        $this->name = $name;
        $this->real_name = $real_name;
        $this->nationality = $nationality;
    }
    
    public function getName(): string {
        return $this->name;
    }

    public function getRealName(): string {
        return $this->real_name;
    }

    public function getNationality(): string {
        return $this->nationality;
    }

}



