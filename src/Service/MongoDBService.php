<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use DateTimeImmutable;

class MongoDBService {

    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient) {
        $this->httpClient = $httpClient;
    }

    public function insertVisit(string $pageName) {
        $this->httpClient->request('POST', 'https://us-east-2.aws.neurelo.com/rest/visits/__one', [
            'headers' => [
                'X-API-KEY' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6ImFybjphd3M6a21zOnVzLWVhc3QtMjowMzczODQxMTc5ODQ6YWxpYXMvYjJjYWNlYWItQXV0aC1LZXkifQ.eyJlbnZpcm9ubWVudF9pZCI6IjM2Y2MxNDQ1LTk2MzEtNGRjNi1iZTM4LWZmNmNmNmMyOWYwMiIsImdhdGV3YXlfaWQiOiJnd19iMmNhY2VhYi0yYTRlLTQ3YzYtOTlkZS1iNDM3M2I4NWE2MjIiLCJwb2xpY2llcyI6WyJSRUFEIiwiV1JJVEUiLCJVUERBVEUiLCJERUxFVEUiLCJDVVNUT00iXSwiaWF0IjoiMjAyNS0wMy0wMVQxNDo0NDowOS4wNzIwNDYyNTFaIiwianRpIjoiOGE0ZWM3ZTItMWU4Zi00ZWUwLWJjMGYtZDI0MGEzZmMwM2Y2In0.G0R7BH9W6qutLkAsSW1emUb_hfZpKn4L05SeadUUVnvIVE_a-VkkgIeZKIv3WV5poT5y2rmlhsUKlpHvAveR1ehfCofVxsQ6zh6rzGe3TyJWrpFwq4EhkRDS27GuNSCKgS00FMr0RZA1fgm-mNuudCSOu5TM_30ZKy6JleXmKbJpO7o7umkrmb5l4dPp_f9GsIgxgiMKQZ8xDIxJEZYZ2OY-qub7FKfi3AzaClhEkC5EzM_ZON4GgZ4rMDRKj86pWfNI_B_4lL9o2TTS1Un_eUSy2ioHlQCqyFKHn2YnPHt1MPfmuqqtpwZEH1eOaGf9kx5pxHROTDrc2TVnnEJt8Q',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'pageName' => $pageName,
                'visitedAt' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ],
        ]);
    }

}