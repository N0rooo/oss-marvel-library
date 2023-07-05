<?php

use PHPUnit\Framework\TestCase;
use Thomasaubert\OssMarvelLibrary\Api;

class ApiTest extends TestCase
{
    public function testGetAllCharacters()
    {
    $client = Symfony\Component\HttpClient\HttpClient::create();
    $api = new Api($client);
    $this->assertIsArray($api->getAllCharacters());
    $characters = $api->getAllCharacters();
    foreach ($characters as $character) {
        $this->assertIsString($character->getName());
        $this->assertIsString($character->getImg());
        $this->assertIsString($character->getLink());
    };

    // test for somes characters
    for ($i = 0; $i < 3; $i++) {
        $this->assertIsArray($api->getInfosOfCharacter($characters[2]->getName(), $characters[2]->getLink()));;
    }
}
    }

