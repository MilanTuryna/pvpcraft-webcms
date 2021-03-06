<?php


namespace App\Presenters;

use App\Model\DI;
use App\Model\Loader\Assets\Module;
use App\Model\Responses\CSSResponse;
use App\Model\Responses\JSResponse;
use App\WebLoader\Exceptions\ParseError;
use Nette\Application\AbortException;
use Nette\Application\Responses\TextResponse;

/**
 * Class WebLoaderPresenter
 * @package App\Presenters
 */
abstract class WebLoaderBasePresenter extends BasePresenter
{
    protected static bool $minifyOutput = true;

    private Module $module;

    /**
     * WebLoaderPresenter constructor.
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        parent::__construct(DI\GoogleAnalytics::disabled());

        $this->module = $module;
    }

    /**
     * @throws AbortException
     */
    public function renderCSS(): void {
        try {
            $this->module->basePath = $this->getHttpRequest()->getUrl()->getHostUrl();
            $cssParser = $this->module->getParsedCSS();
            $parsedCSS = $cssParser->getComputedCode(self::$minifyOutput);
            $response = new CSSResponse($parsedCSS);
            $this->sendResponse($response);
        } catch (ParseError $e) {
            $textResponse = new TextResponse($e->getMessage());
            $this->sendResponse($textResponse);
        }

    }

    /**
     * @throws AbortException
     */
    public function renderJS(): void {
        try {
            $this->sendResponse(new JSResponse($this->module->getParsedJS()));
        } catch (ParseError $parseError) {
            $textResponse = new TextResponse($parseError->getMessage());
            $this->sendResponse($textResponse);
        }
    }
}