<?php
namespace Sitegeist\Nomenclator\Fusion;

use Neos\ContentRepository\Domain\Projection\Content\TraversableNodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Service\LinkingService;
use Sitegeist\Nomenclator\Domain\Glossary;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Neos\Eel\FlowQuery\FlowQuery;

class LinkTermsToGlossaryImplementation extends AbstractFusionObject
{

    /**
     * @Flow\Inject
     * @var Glossary
     */
    protected $glossary;

    /**
     * @Flow\Inject
     * @var LinkingService
     */
    protected $linkingService;

    /**
     * @param TraversableNodeInterface $documentNode
     * @param bool $absolute
     * @return string
     */

    protected function getNodeUri(TraversableNodeInterface $documentNode) {
        $controllerContext = $this->runtime->getControllerContext();
        $resolvedUri = $this->linkingService->createNodeUri($controllerContext,$documentNode);
        return $resolvedUri;
    }


    /**
     * Glossary page and glossary root component of the site
     *
     * @return array
     */
    public function getGlossaryNodes()
    {
        /** @var $contentContext ContentContext */
        $contentContext = $this->getNode()->getContext();

        $site = $contentContext->getCurrentSiteNode();

        $glossaryNodes = (new FlowQuery([$site]))->find('[instanceof Sitegeist.Nomenclator:Content.Glossary]')->get();

        $glossaryPages = (new FlowQuery([$site]))->find('[instanceof Sitegeist.Nomenclator:Document.Glossary]')->get();

        $glossary['node'] = $glossaryNodes[0];

        $glossary['page'] = $glossaryPages[0];

        return $glossary;
    }

    /**
     * The string to be processed
     *
     * @return string
     */
    public function getValue()
    {
        return $this->fusionValue('value');
    }

    /**
     * the node to be processed
     *
     * @return TraversableNodeInterface
     */
    public function getNode()
    {
        return $this->fusionValue('node');
    }


    public function evaluate()
    {


        $glossary = $this->getGlossaryNodes();

        $isInBackend = $this->getNode()->getContext()->isInBackend();

        $content = $this->getValue();

        if(!$glossary['node']->getProperty('linkingActivated') || $isInBackend || !$glossary['node']) {

            return $content;
        }

        $glossaryIndex = $this->glossary->readGlossaryIndexFromCache($glossary['node']);

        $glossaryUri = $this->getNodeUri($glossary['page']);

        $lastBracket = '';

        $processedContent = "" ;

        while(strlen($content)){

            $nextLaBracket = (strpos($content,'<') === false) ? strlen($content) - 1 : strpos($content,'<') ;
            $nextRaBracket = (strpos($content,'>') === false) ? strlen($content) - 1 : strpos($content,'>') ;

            $currentPosition = min ($nextLaBracket , $nextRaBracket) ;

            $currentBracket = $content[$currentPosition];

            $buffer = substr($content,0,++$currentPosition);

            $content = substr($content, $currentPosition);


            if($lastBracket == '<' && $currentBracket == '>') {

                $processedContent = $processedContent . $buffer;

            } else {
                foreach ($glossaryIndex['terms'] as $term => $identifier) {

                    $pattern = '/\b'.$term.'\b/i';

                    $buffer = preg_replace_callback ($pattern , function($matches) use ($glossaryIndex, $identifier, $glossaryUri){

                        return sprintf('<a class="nomenclator_entry" href="%s#%s" data-identifier="%s">%s</a>', $glossaryUri, $glossaryIndex['titles'][(string)$identifier],$identifier ,$matches[0]);


                    },
                    $buffer);

                }

                $processedContent = $processedContent . $buffer;
            }

            $lastBracket=$currentBracket;

        }

        return $processedContent;

    }

}
