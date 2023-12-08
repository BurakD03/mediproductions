<?php

declare(strict_types=1);

namespace App\Context\Api\Admin;

use App\Entity\Licence\Licence;
use Behat\Behat\Context\Context;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\RequestStack;

final class AjaxContextLicence implements Context
{
    public function __construct(
        private AbstractBrowser $client,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @When I look for a variant with :phrase in descriptor within the :product product
     */
    public function getLicenceCodeCrm( $phrase, Licence $licence): void
    {
        $this->client->getCookieJar()->set(new Cookie($this->requestStack->getSession()->getName(), $this->requestStack->getSession()->getId()));
        $this->client->request(
            'GET',
            '/admin/order/search',
            ['phrase' => $phrase, 'productCode' => $licence->getCodeCrm()],
            [],
            ['ACCEPT' => 'application/json'],
        );
    }

}