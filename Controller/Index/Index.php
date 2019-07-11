<?php
namespace Swimwear\Fixme\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /** @var \Swimwear\Fixme\Helper\Request */
    private $requestHelper;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Swimwear\Fixme\Helper\Request $requestHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Swimwear\Fixme\Helper\Request $requestHelper
    ) {
        $this->requestHelper = $requestHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->requestHelper->sendRequest('https://maps.googleapis.com/maps/api/geocode/json', null);
        var_dump($response);
    }
}
