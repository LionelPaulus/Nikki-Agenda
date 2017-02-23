<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class GoogleContactsService
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getAllEmails($accessToken)
    {
        $client = $this->container->get('happyr.google.api.client');

        $client->setAccessToken($accessToken);

        $groups = file_get_contents('https://www.google.com/m8/feeds/groups/default/full?v=3.0&alt=json&access_token='.$accessToken['access_token']);
        $groups = json_decode($groups, true);
        $groupId = $groups['feed']['entry'][0]['id']['$t'];

        $contacts = file_get_contents('https://www.google.com/m8/feeds/contacts/default/full?group='.$groupId.'&alt=json&max-results=1000&access_token='.$accessToken['access_token']);
        $contacts = json_decode($contacts, true);

        $result = [];
        foreach ($contacts['feed']['entry'] as $contact) {
            if (isset($contact['gd$email'][0]['address'])) {
                array_push($result, $contact['gd$email'][0]['address']);
            }
        }

        return $result;
    }
}
