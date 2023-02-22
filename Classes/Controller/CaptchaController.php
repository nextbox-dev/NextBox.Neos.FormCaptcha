<?php

namespace NextBox\Neos\FormCaptcha\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Session\SessionInterface;
use Neos\Utility\Files;

class CaptchaController extends ActionController
{
    public const CAPTCHA_SESSION_NAME = 'form_captcha.captcha_code';

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
     * Create a captcha image
     *
     * @return void
     */
    public function createCaptchaAction(): void
    {
        $captchaCode = '';
        $captchaImageHeight = $this->settings['image']['height'];
        $captchaImageWidth = $this->settings['image']['width'];
        $totalCharactersOnImage = $this->settings['totalCharacters'];

        $possibleCaptchaLetters = $this->settings['possibleLetters'];
        $captchaFont = Files::concatenatePaths([
            FLOW_PATH_PACKAGES,
            'Plugins',
            'NextBox.Neos.FormCaptcha',
            'Resources',
            'Private',
            'Fonts',
            'monofont.ttf',
        ]);

        $randomCaptchaDots = $this->settings['random']['dots'];
        $randomCaptchaLines = $this->settings['random']['lines'];
        $textColor = $this->settings['textColor'];
        $noiseColor = $this->settings['noiseColor'];
        $backgroundColor = $this->settings['backgroundColor'];

        $i = 0;
        while ($i < $totalCharactersOnImage) {
            $captchaCode .= substr($possibleCaptchaLetters, mt_rand(0, strlen($possibleCaptchaLetters) - 1), 1);
            $i++;
        }

        $captchaFontSize = $captchaImageHeight * 0.65;
        $captchaImage = @imagecreate($captchaImageWidth, $captchaImageHeight);

        $background = imagecolorallocate($captchaImage, $backgroundColor['r'], $backgroundColor['g'], $backgroundColor['b']);

        $textColor = imagecolorallocate($captchaImage, $textColor['r'], $textColor['g'], $textColor['b']);

        $noiseColor = imagecolorallocate($captchaImage, $noiseColor['r'], $noiseColor['g'], $noiseColor['b']);

        /* generating the dots randomly */
        for ($i = 0; $i < $randomCaptchaDots; $i++) {
            imagefilledellipse($captchaImage, mt_rand(0, $captchaImageWidth), mt_rand(0, $captchaImageHeight), 2, 3, $noiseColor);
        }

        /* generating lines randomly*/
        for ($i = 0; $i < $randomCaptchaLines; $i++) {
            imageline($captchaImage, mt_rand(0, $captchaImageWidth), mt_rand(0, $captchaImageHeight), mt_rand(0, $captchaImageWidth), mt_rand(0, $captchaImageHeight), $noiseColor);
        }

        /* create a text box and add letters in it */
        $textBox = imagettfbbox($captchaFontSize, 0, $captchaFont, $captchaCode);
        $x = ($captchaImageWidth - $textBox[4]) / 2;
        $y = ($captchaImageHeight - $textBox[5]) / 2;
        imagettftext($captchaImage, $captchaFontSize, 0, $x, $y, $textColor, $captchaFont, $captchaCode);

        header('Content-Type: image/jpeg');
        header('Pragma: no-cache');
        header('Cache-Control: no-cache');

        imagejpeg($captchaImage);
        imagedestroy($captchaImage);

        // Save the captcha code in the session
        $this->session->putData(self::CAPTCHA_SESSION_NAME, $captchaCode);
    }
}
