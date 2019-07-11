<?php
namespace Swimwear\Fixme\Helper;

class Request extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    ) {
        $this->httpClientFactory = $httpClientFactory;
        parent::__construct($context, $storeManager);
    }

    /**
     * @param $url
     * @param $data
     * @return mixed
     * @throws \Exception
     * @throws \Zend_Http_Client_Exception
     */
    public function sendRequest($url, $data)
    {
        /** @var \Magento\Framework\HTTP\ZendClient $client */
        $client = $this->httpClientFactory->create();

        $client->setUri($url);
        $client->setConfig(['timeout' => 30]);
        $client->setMethod(\Zend_Http_Client::GET);
        $client->setParameterGet($data);
        $client->setUrlEncodeBody(false);

        try {
            $response = $client->request();
            $responseBody = $response->getBody();
        } catch (\Exception $e) {
            // Currently catches error:
            //   Invalid header line detected
            return $e->getMessage();
        }

        // Should be getting here and return:
        //   Invalid request. Missing the 'address', 'components', 'latlng' or 'place_id' parameter
        return json_decode($responseBody);
    }
}