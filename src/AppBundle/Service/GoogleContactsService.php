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

        // $contacts = file_get_contents('https://www.google.com/m8/feeds/contacts/default/full?alt=json&max-results=1000&group=default&access_token='.$accessToken['access_token']);
        // $contacts = file_get_contents('https://www.google.com/m8/feeds/default/full/Contacts?alt=json&max-results=500&access_token='.$accessToken['access_token']);
        // $contacts = file_get_contents('https://www.google.com/m8/feeds/groups/default/full?v=3.0&alt=json&access_token='.$accessToken['access_token']);
        $contacts = file_get_contents('https://www.google.com/m8/feeds/contacts/default/full?group=http://www.google.com/m8/feeds/groups/lionelpaulus%40gmail.com/base/6&v=3.0&alt=json&access_token='.$accessToken['access_token']);
        echo $contacts;
        die();
        $contacts = json_decode($contacts, true);
        $num = 0;
        foreach ($contacts['feed']['entry'] as $contact) {
            $num++;
            if (isset($contact['gd$email'][0]['address'])) {
                echo '<br>'.$num.' - '.$contact['gd$email'][0]['address'];
            }
        }
        die();
        $service = new \Google_Service_People($client->getGoogleClient());

        // Print the names for up to 10 connections.
        $optParams = array(
          'pageSize' => 500
        );
        $results = $service->people_connections->listPeopleConnections('people/me', $optParams);

        if (count($results->getConnections()) == 0) {
            print "No connections found.\n";
        } else {
            foreach ($results->getConnections() as $person) {
                if (count($person->getEmailAddresses()) == 0) {
                    print "No email found for this connection\n";
                } else {
                    $emails = $person->getEmailAddresses();
                    echo '<pre>';
                    var_dump($emails);
                    echo '</pre>';
                    // printf("%s\n", $emails[0]);
                }
            }
        }
        die();
        return $contactsArray;
    }
}
