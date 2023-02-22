<?php

namespace NextBox\Neos\FormCaptcha\Form\FormElements;

use GuzzleHttp\Psr7\ServerRequest;
use Neos\Error\Messages\Error;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Mvc\Routing\UriBuilder;
use Neos\Flow\Session\SessionInterface;
use Neos\Form\Core\Model\AbstractFormElement;
use Neos\Form\Core\Runtime\FormRuntime;
use NextBox\Neos\FormCaptcha\Controller\CaptchaController;

class Captcha extends AbstractFormElement
{
    /**
     * @Flow\Inject
     * @var SessionInterface
     */
    protected $session;

    /**
     * @Flow\InjectConfiguration
     * @var array
     */
    protected $settings;

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * Initialize form element
     * Add the captcha uri to the rendering options
     * Set the max length based on the total characters for captchas
     *
     * @param FormRuntime $formRuntime
     * @return void
     * @throws \Neos\Flow\Http\Exception
     * @throws \Neos\Flow\Mvc\Routing\Exception\MissingActionNameException
     */
    public function beforeRendering(FormRuntime $formRuntime)
    {
        parent::beforeRendering($formRuntime);

        // get the uri for the captcha image
        $this->setRenderingOption(
            '_captchaUri',
            $this->getUriBuilder()->uriFor(
                'createCaptcha',
                [],
                'Captcha',
                'NextBox.Neos.FormCaptcha'
            )
        );

        // Set max length based on the total characters for captchas
        $this->setRenderingOption(
            '_maxlength',
            $this->settings['totalCharacters']
        );
    }

    /**
     * Validate captcha
     *
     * @param FormRuntime $formRuntime
     * @param $elementValue
     * @return void
     * @throws \Neos\Flow\Session\Exception\SessionNotStartedException
     * @throws \Neos\Form\Exception\FormDefinitionConsistencyException
     */
    public function onSubmit(FormRuntime $formRuntime, &$elementValue)
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        if ($this->session->hasKey(CaptchaController::CAPTCHA_SESSION_NAME)) {
            $phrase = $this->session->getData(CaptchaController::CAPTCHA_SESSION_NAME);

            if ($elementValue !== $phrase) {
                $processingRule = $this->getRootForm()->getProcessingRule($this->getIdentifier());
                $processingRule->getProcessingMessages()->addError(new Error('The captcha is not equal to the image.', 1677000600));
            }
        }
    }

    /**
     * Get UriBuilder
     *
     * @return UriBuilder
     */
    protected function getUriBuilder(): UriBuilder
    {
        if (!$this->uriBuilder instanceof UriBuilder) {
            $request = ServerRequest::fromGlobals();
            $actionRequest = ActionRequest::fromHttpRequest($request);

            $this->uriBuilder = new UriBuilder();
            $this->uriBuilder->setRequest($actionRequest);
            $this->uriBuilder->setCreateAbsoluteUri(true);
        }

        return $this->uriBuilder;
    }
}
